<?php namespace Thenextweb;

use Storage;
use Log;
use InvalidArgumentException;

class PassGenerator
{

    /**
     * The store with the pass ID certificate
     */
    private $cert_store;

    /**
     * The password to unlock the certificate store
     */
    private $cert_store_password;

    /**
     * Path to the Apple Worldwide Developer Relations Intermediate Certificate
     */
    private $wwdr_cert_path;

    /**
     * The JSON definition for the pass (pass.json)
     */
    private $pass_json;

    /**
     * All the assets (images) to be included on the pass.
     */
    private $assets;

    /**
     * All the localized assets (strings and images)
     */
    private $localized_assets;

    /**
     * The localizations included on the pass.
     */
    private $localizations;

    /**
     * Filename for the pass. If provided, it'll be the pass_id with .pkpass
     * extension, otherwise a random name will be assigned.
     */
    private $pass_filename;

    /**
     * Relative path to the pass on its temp folder.
     */
    private $pass_relative_path;

    /**
     * Real path to the pass on its temp folder.
     */
    private $pass_real_path;

    /**
     * Some filenames as defined by Apple
     */
    private $signature_filename = 'signature';
    private $manifest_filename = 'manifest.json';
    private $pass_json_filename = 'pass.json';

    /**
     * Constructor
     *
     * @param string $pass_id [optional] If given, it'll be used to name the pass file.
     * @param bool $replace_existent [optional] If true, it'll replace any existing pass with the same filename.
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function __construct($pass_id = false, $replace_existent = false)
    {
        // Set certificate
        if (is_file(config('passgenerator.certificate_store_path'))) {
            $this->cert_store = file_get_contents(config('passgenerator.certificate_store_path'));
        } else {
            throw new InvalidArgumentException("No certificate found on " . config('passgenerator.certificate_store_path'));
        }

        // Set password
        $this->cert_store_password = config('passgenerator.certificate_store_password');


        if (is_file(config('passgenerator.wwdr_certificate_path')) && @openssl_x509_read(file_get_contents(config('passgenerator.wwdr_certificate_path')))) {
            $this->wwdr_cert_path= config('passgenerator.wwdr_certificate_path');
        } else {
            $error_msg = "No valid intermediate certificate was found on " . config('passgenerator.wwdr_certificate_path'). PHP_EOL;
            $error_msg .= "The WWDR intermediate certificate must be on PEM format, ";
            $error_msg .= "the DER version can be found at https://www.apple.com/certificateauthority/ ";
            $error_msg .= "But you'll need to export it into PEM.";
            throw new InvalidArgumentException($error_msg);
        }

        $this->assets = [];

        if (!$pass_id) {
            $pass_id = uniqid('pass_', true);
        }
        $this->pass_relative_path = "$pass_id";
        $this->pass_filename = "$pass_id.pkpass";
        if (Storage::disk('passgenerator')->has($this->pass_filename)) {
            if ($replace_existent) {
                Storage::disk('passgenerator')->delete($this->pass_filename);
            } else {
                throw new \RuntimeException("The file {$this->pass_filename} already exists, try another pass_id or download.");
            }
        }
        $this->pass_real_path = Storage::disk('passgenerator')->getDriver()->getAdapter()->getPathPrefix() . $this->pass_relative_path;
    }

    /**
     * Clean up the temp folder if the execution was stopped for some reason
     * If it was already removed, nothing happens
     */
    public function __destruct()
    {
        Storage::disk('passgenerator')->deleteDirectory($this->pass_relative_path);
    }

    /**
     * Add an asset to the pass. Use this function to add images to the pass.
     *
     * @param string $asset_path
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function addAsset($asset_path)
    {
        if (is_file($asset_path)) {
            $this->assets[basename($asset_path)] = $asset_path;
            return;
        }
        throw new InvalidArgumentException("The file $asset_path does NOT exist");
    }

    /**
     * Add localized assets to the pass.
     *
     * @param string $asset_path
     * @param string $localization The localization to be used
     *
     * @note NOT SUPPORTED YET
     * @todo ADD IMPLEMENTATION FOR LOCALIZATION
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function addLocalizedAssets($asset_path, $localization)
    {
        throw new \RuntimeException("Not implemented yet");
        if (is_file($asset_path)) {
            $this->localized_assets[$localization][basename($asset_path)] = $asset_path;
            if (!in_array($localization, $this->localizations)) {
                $this->localizations[] = $localization;
            }
            return;
        }
        throw new InvalidArgumentException("The file $asset_path does NOT exist");
    }

    /**
     * Set the pass definition with an array.
     *
     * @param array $definition
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function setPassDefinition($definition)
    {
        if (!is_array($definition)) {
            throw new InvalidArgumentException("An invalid Pass definition was provided.");
        }
        $this->pass_json = json_encode($definition);
    }

    /**
     * Set the pass definition with a JSON.
     *
     * @param string $json_defintion
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function setPassDefinitionJson($json_defintion)
    {
        if (!json_decode($json_defintion)) {
            throw new InvalidArgumentException("An invalid JSON Pass definition was provided.");
        }
        $this->pass_json = $json_defintion;
    }

    /**
     * description
     *
     * @param param-type $param-name
     *
     * @return return-type
     */
    public function create()
    {
        $this->create_temp_folder();

        // Create and store the json manifest
        $manifest = $this->createJsonManifest();
        Storage::disk('passgenerator')->put($this->pass_relative_path. '/manifest.json', $manifest);

        // Sign manifest with the certificate
        $this->signManifest();

        // Create the actual pass
        $this->zipItAll();

        // Get it out of the tmp folder and clean everything up
        Storage::disk('passgenerator')->move($this->pass_relative_path. '/' . $this->pass_filename, $this->pass_filename);
        Storage::disk('passgenerator')->deleteDirectory($this->pass_relative_path);

        // Return the contents, but keep the pkpass stored for future downloads
        return Storage::disk('passgenerator')->get($this->pass_filename);
    }

    /**
     * Get a pass if it was already created.
     *
     * @param string $pass_id
     *
     * @return String|bool If exists, the content of the pass.
     */
    public static function getPass($pass_id)
    {
        if (Storage::disk('passgenerator')->has($pass_id . '.pkpass')) {
            return Storage::disk('passgenerator')->get($pass_id . '.pkpass');
        }
        return false;
    }

    /**
     * Get the path to a pass if it was already created.
     *
     * @param string $pass_id
     *
     * @return string|bool
     */
    public static function getPassFilePath($pass_id)
    {
        if (Storage::disk('passgenerator')->has($pass_id . '.pkpass')) {
            return $this->pass_real_path . "/../" . $this->pass_filename;
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
        $hashes['pass.json'] = sha1($this->pass_json);
        foreach ($this->assets as $filename => $path) {
            $hashes[$filename] = sha1(file_get_contents($path));
        }

//      // TODO: Add support for localization
//         foreach($this->localizations as $localization) {
//             foreach($this->localized_assets[$localization] as $filename => $path) {
//                 $hashes[$filename] = sha1(file_get_contents($path));
//             }
//         }
        return json_encode((object)$hashes);
    }

    /**
     * Remove all the MIME and email crap around the DER signature and decode it from base64.
     *
     * @param string $signature The returned result of openssl_pkcs7_sign()
     *
     * @return string A clean DER signature
     */
    private function removeMimeBS($email_signature)
    {
        $last_header_line = 'Content-Disposition: attachment; filename="smime.p7s"';
        $footer_line_start = "\n------";

        // Remove first the header, first find the new-line on the last line of the header and cut all the previous
        $first_signature_line = mb_strpos($email_signature, "\n", mb_strpos($email_signature, $last_header_line));
        $clean_signature = mb_strcut($email_signature, $first_signature_line+1);

        // Now remove the 'footer',
        $end_of_signature = mb_strpos($clean_signature, $footer_line_start);
        $clean_signature = mb_strcut($clean_signature, 0, $end_of_signature);

        // Clean and decode
        $clean_signature = trim($clean_signature);
        return base64_decode($clean_signature);
    }

    /**
     * Sign the manifest with the provided certificates and store the signature.
     *
     * @see -) http://php.net/manual/en/function.openssl-pkcs7-sign.php#111336 for PKCS7 flags.
     *      -) https://en.wikipedia.org/wiki/X.509 for further info on PEM, DER and other certificate stuff
     *      -) http://php.net/manual/en/function.openssl-pkcs7-sign.php for the return of signing function
     *      -) and a google "smime.p7s" for further fun... on how broken cryptography on the internet is.
     *
     * @throws \RuntimeException
     */
    private function signManifest()
    {
        $manifest_path = $this->pass_real_path . '/' . $this->manifest_filename;
        $signature_path = $this->pass_real_path . '/' . $this->signature_filename;

        $certs = [];
        if (openssl_pkcs12_read($this->cert_store, $certs, $this->cert_store_password)) {
            // Get the certificate resource
            $cert_resource = openssl_x509_read($certs['cert']);
            // Get the private key out of the cert
            $private_key = openssl_pkey_get_private($certs['pkey'], $this->cert_store_password);
            // Sign the manifest and store int in the signature file

            openssl_pkcs7_sign($manifest_path, $signature_path, $cert_resource, $private_key, [], PKCS7_BINARY | PKCS7_DETACHED, $this->wwdr_cert_path);

            /**
             * PKCS7 returns a signature on PEM format (.p7s), we only need the DER signature so Apple does not cry
             * It turns out we are lucky since p7s format is just a Base64 encoded DER signature
             * enclosed between some email headers a MIME bs, so we just need to remove some lines
             */
            $signature = Storage::disk('passgenerator')->get($this->pass_relative_path.'/'.$this->signature_filename);
            $signature = $this->removeMimeBS($signature);
            Storage::disk('passgenerator')->put($this->pass_relative_path.'/'.$this->signature_filename, $signature);
            return;
        }

        throw new \RuntimeException("The certificate could not be read.");
    }

    /**
     * Create a the pass zipping all files into one
     *
     * @throws \RuntimeException
     */
    private function zipItAll()
    {
        $zip_path = $this->pass_real_path . '/' . $this->pass_filename;
        $manifest_path = $this->pass_real_path . '/' . $this->manifest_filename;
        $signature_path = $this->pass_real_path . '/' . $this->signature_filename;

        $zip = new \ZipArchive();
        if (!$zip->open($zip_path, \ZipArchive::CREATE)) {
            throw new \RuntimeException("There was a problem while creating the zip file");
        }

        $zip->addFile($manifest_path, $this->manifest_filename);
        $zip->addFile($signature_path, $this->signature_filename);
        $zip->addFromString($this->pass_json_filename, $this->pass_json);
        foreach ($this->assets as $name => $path) {
            $zip->addFile($path, $name);
        }
        $zip->close();
    }

    /*
     * Create a temporary folder to store all files before creating the pass.
     */
    private function create_temp_folder()
    {
        if (!is_dir($this->pass_real_path)) {
            Storage::disk('passgenerator')->makeDirectory($this->pass_relative_path);
        }
    }
}
