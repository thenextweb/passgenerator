<?php

namespace Thenextweb;

use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;
use Thenextweb\Definitions\DefinitionInterface;
use ZipArchive;

class PassGenerator
{
    /**
     * The store with the pass ID certificate.
     */
    private $certStore;

    /**
     * The password to unlock the certificate store.
     */
    private $certStorePassword;

    /**
     * Path to the Apple Worldwide Developer Relations Intermediate Certificate.
     */
    private $wwdrCertPath;

    /**
     * The JSON definition for the pass (pass.json).
     */
    private $passJson;

    /**
     * All the assets (images) to be included on the pass.
     */
    private $assets = [];

    /**
     * All the localized assets (strings and images).
     */
    private $localizedAssets;

    /**
     * The localizations included on the pass.
     */
    private $localizations;

    /**
     * Filename for the pass. If provided, it'll be the pass_id with .pkpass
     * extension, otherwise a random name will be assigned.
     */
    private $passFilename;

    /**
     * Relative path to the pass on its temp folder.
     */
    private $passRelativePath;

    /**
     * Real path to the pass on its temp folder.
     */
    private $passRealPath;

    /**
     * Some file names as defined by Apple.
     */
    private $signatureFilename = 'signature';
    private $manifestFilename = 'manifest.json';
    private $passJsonFilename = 'pass.json';

    /**
     * Constructor.
     *
     * @param bool|string $passId          [optional] If given, it'll be used to name the pass file.
     * @param bool        $replaceExistent [optional] If true, it'll replace any existing pass with the same filename.
     */
    public function __construct($passId = false, $replaceExistent = false)
    {
        // Set certificate
        $certPath = config('passgenerator.certificate_store_path');

        if (is_file($certPath)) {
            $this->certStore = file_get_contents($certPath);
        } else {
            throw new InvalidArgumentException(
                'No certificate found on ' . $certPath
            );
        }

        // Set password
        $this->certStorePassword = config('passgenerator.certificate_store_password');

        // Set WWDR certificate
        $wwdrCertPath = config('passgenerator.wwdr_certificate_path');

        if (is_file($wwdrCertPath) && @openssl_x509_read(file_get_contents($wwdrCertPath))) {
            $this->wwdrCertPath = $wwdrCertPath;
        } else {
            $errorMsg = 'No valid intermediate certificate was found on ' . $wwdrCertPath . PHP_EOL;
            $errorMsg .= 'The WWDR intermediate certificate must be on PEM format, ';
            $errorMsg .= 'the DER version can be found at https://www.apple.com/certificateauthority/ ';
            $errorMsg .= "But you'll need to export it into PEM.";

            throw new InvalidArgumentException($errorMsg);
        }

        if (!$passId) {
            $passId = uniqid('pass_', true);
        }

        $this->passRelativePath = $passId;

        $this->passFilename = $passId . '.pkpass';

        if (Storage::disk('passgenerator')->has($this->passFilename)) {
            if ($replaceExistent) {
                Storage::disk('passgenerator')->delete($this->passFilename);
            } else {
                throw new RuntimeException(
                    'The file ' . $this->passFilename . ' already exists, try another pass_id or download.'
                );
            }
        }

        $this->passRealPath = Storage::disk('passgenerator')
                ->getDriver()
                ->getAdapter()
                ->getPathPrefix() . $this->passRelativePath;
    }

    /**
     * Clean up the temp folder if the execution was stopped for some reason
     * If it was already removed, nothing happens.
     */
    public function __destruct()
    {
        Storage::disk('passgenerator')->deleteDirectory($this->passRelativePath);
    }

    /**
     * Add an asset to the pass. Use this function to add images to the pass.
     *
     * @param string $assetPath
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function addAsset($assetPath)
    {
        if (is_file($assetPath)) {
            $this->assets[basename($assetPath)] = $assetPath;

            return;
        }

        throw new InvalidArgumentException("The file $assetPath does NOT exist");
    }

    /**
     * Add localized assets to the pass.
     *
     * @param string $assetPath
     * @param string $localization The localization to be used
     *
     * @note NOT SUPPORTED YET
     *
     * @todo ADD IMPLEMENTATION FOR LOCALIZATION
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function addLocalizedAssets($assetPath, $localization)
    {
        throw new RuntimeException('Not implemented yet');

//        if (!is_file($assetPath)) {
//            throw new InvalidArgumentException("The file $assetPath does NOT exist");
//        }
//
//        $this->localizedAssets[$localization][basename($assetPath)] = $assetPath;
//
//        if (!in_array($localization, $this->localizations)) {
//            $this->localizations[] = $localization;
//        }
    }

    /**
     * Set the pass definition with an array.
     *
     * @param array $definition
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function setPassDefinition($definition)
    {
        if ($definition instanceof DefinitionInterface) {
            $definition = $definition->getPassDefinition();
        }

        if (!is_array($definition)) {
            throw new InvalidArgumentException('An invalid Pass definition was provided.');
        }

        $this->passJson = json_encode($definition);
    }

    /**
     * Set the pass definition with a JSON string.
     *
     * @param string $jsonDefinition
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function setPassDefinitionJson($jsonDefinition)
    {
        if (!json_decode($jsonDefinition)) {
            throw new InvalidArgumentException('An invalid JSON Pass definition was provided.');
        }

        $this->passJson = $jsonDefinition;
    }

    /**
     * Create the signed .pkpass file.
     *
     * @return string
     */
    public function create()
    {
        $this->createTempFolder();

        // Create and store the json manifest
        $manifest = $this->createJsonManifest();

        Storage::disk('passgenerator')->put($this->passRelativePath . '/manifest.json', $manifest);

        // Sign manifest with the certificate
        $this->signManifest();

        // Create the actual pass
        $this->zipItAll();

        // Get it out of the tmp folder and clean everything up
        Storage::disk('passgenerator')->move($this->passRelativePath . '/' . $this->passFilename, $this->passFilename);

        Storage::disk('passgenerator')->deleteDirectory($this->passRelativePath);

        // Return the contents, but keep the pkpass stored for future downloads
        return Storage::disk('passgenerator')->get($this->passFilename);
    }

    /**
     * Get a pass if it was already created.
     *
     * @param string $passId
     *
     * @return string|bool If exists, the content of the pass.
     */
    public static function getPass($passId)
    {
        if (Storage::disk('passgenerator')->has($passId . '.pkpass')) {
            return Storage::disk('passgenerator')->get($passId . '.pkpass');
        }

        return false;
    }

    /**
     * Get the path to a pass if it was already created.
     *
     * @param string $passId
     *
     * @return string|bool
     */
    public function getPassFilePath($passId)
    {
        if (Storage::disk('passgenerator')->has($passId . '.pkpass')) {
            return $this->passRealPath . '/../' . $this->passFilename;
        }

        return false;
    }

    /**
     * Get the valid MIME type for the pass.
     *
     * @return string
     */
    public static function getPassMimeType()
    {
        return 'application/vnd.apple.pkpass';
    }

    /**
     * Create the JSON manifest with all the hashes from the included files.
     */
    private function createJsonManifest()
    {
        $hashes['pass.json'] = sha1($this->passJson);

        foreach ($this->assets as $filename => $path) {
            $hashes[$filename] = sha1(file_get_contents($path));
        }

//      // TODO: Add support for localization
//         foreach($this->localizations as $localization) {
//             foreach($this->localizedAssets[$localization] as $filename => $path) {
//                 $hashes[$filename] = sha1(file_get_contents($path));
//             }
//         }

        return json_encode((object) $hashes);
    }

    /**
     * Remove all the MIME and email crap around the DER signature and decode it from base64.
     *
     * @param $emailSignature
     *
     * @return string A clean DER signature
     *
     * @internal param string $signature The returned result of openssl_pkcs7_sign()
     */
    private function removeMimeBS($emailSignature)
    {
        $lastHeaderLine = 'Content-Disposition: attachment; filename="smime.p7s"';

        $footerLineStart = "\n------";

        // Remove first the header, first find the new-line on the last line of the header and cut all the previous
        $firstSignatureLine = mb_strpos($emailSignature, "\n", mb_strpos($emailSignature, $lastHeaderLine));

        $cleanSignature = mb_strcut($emailSignature, $firstSignatureLine + 1);

        // Now remove the 'footer',
        $endOfSignature = mb_strpos($cleanSignature, $footerLineStart);

        $cleanSignature = mb_strcut($cleanSignature, 0, $endOfSignature);

        // Clean and decode
        $cleanSignature = trim($cleanSignature);

        return base64_decode($cleanSignature);
    }

    /**
     * Sign the manifest with the provided certificates and store the signature.
     *
     * @see -) http://php.net/manual/en/function.openssl-pkcs7-sign.php#111336 for PKCS7 flags.
     *      -) https://en.wikipedia.org/wiki/X.509 for further info on PEM, DER and other certificate stuff
     *      -) http://php.net/manual/en/function.openssl-pkcs7-sign.php for the return of signing function
     *      -) and a google "smime.p7s" for further fun... on how broken cryptography on the internet is.
     *
     * @throws RuntimeException
     */
    private function signManifest()
    {
        $manifestPath = $this->passRealPath . '/' . $this->manifestFilename;

        $signaturePath = $this->passRealPath . '/' . $this->signatureFilename;

        $certs = [];

        if (!openssl_pkcs12_read($this->certStore, $certs, $this->certStorePassword)) {
            throw new RuntimeException('The certificate could not be read.');
        }

        // Get the certificate resource
        $certResource = openssl_x509_read($certs['cert']);

        // Get the private key out of the cert
        $privateKey = openssl_pkey_get_private($certs['pkey'], $this->certStorePassword);

        // Sign the manifest and store int in the signature file
        openssl_pkcs7_sign(
            $manifestPath,
            $signaturePath,
            $certResource,
            $privateKey,
            [],
            PKCS7_BINARY | PKCS7_DETACHED,
            $this->wwdrCertPath
        );

        // PKCS7 returns a signature on PEM format (.p7s), we only need the DER signature so Apple does not cry.
        // It turns out we are lucky since p7s format is just a Base64 encoded DER signature
        // enclosed between some email headers a MIME bs, so we just need to remove some lines
        $signature = Storage::disk('passgenerator')->get($this->passRelativePath . '/' . $this->signatureFilename);

        $signature = $this->removeMimeBS($signature);

        Storage::disk('passgenerator')->put($this->passRelativePath . '/' . $this->signatureFilename, $signature);
    }

    /**
     * Create a the pass zipping all files into one.
     *
     * @throws RuntimeException
     */
    private function zipItAll()
    {
        $zipPath = $this->passRealPath . '/' . $this->passFilename;

        $manifestPath = $this->passRealPath . '/' . $this->manifestFilename;

        $signaturePath = $this->passRealPath . '/' . $this->signatureFilename;

        $zip = new ZipArchive();

        if (!$zip->open($zipPath, ZipArchive::CREATE)) {
            throw new RuntimeException('There was a problem while creating the zip file');
        }

        // Add the manifest
        $zip->addFile($manifestPath, $this->manifestFilename);

        // Add the signature
        $zip->addFile($signaturePath, $this->signatureFilename);

        // Add pass.json
        $zip->addFromString($this->passJsonFilename, $this->passJson);

        // Add all the assets
        foreach ($this->assets as $name => $path) {
            $zip->addFile($path, $name);
        }

        $zip->close();
    }

    /*
     * Create a temporary folder to store all files before creating the pass.
     */
    private function createTempFolder()
    {
        if (!is_dir($this->passRealPath)) {
            Storage::disk('passgenerator')->makeDirectory($this->passRelativePath);
        }
    }
}
