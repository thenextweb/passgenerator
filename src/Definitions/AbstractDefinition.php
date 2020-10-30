<?php

namespace Thenextweb\Definitions;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Fluent;
use Thenextweb\Definitions\Dictionary\Barcode;
use Thenextweb\Definitions\Dictionary\Beacon;
use Thenextweb\Definitions\Dictionary\Field;
use Thenextweb\Definitions\Dictionary\Location;
use Thenextweb\Definitions\Dictionary\Nfc;

abstract class AbstractDefinition extends Fluent implements DefinitionInterface
{

    /** @var array */
    protected $pass = [];

    /** @var int */
    protected $formatVersion = 1;

    /** @var string */
    protected $style;

    public function __construct(array $attributes = [])
    {
        $default = [
            'formatVersion' => $this->formatVersion,
            'passTypeIdentifier' => config('passgenerator.pass_type_identifier', ''),
            'organizationName' => config('passgenerator.organization_name', ''),
            'teamIdentifier' => config('passgenerator.team_identifier', ''),
        ];
        parent::__construct(array_merge($default, $attributes));
    }
    /**
     * Brief description of the pass, used by the iOS accessibility technologies.
     *
     * Don’t try to include all of the data on the pass in its description, just
     * include enough detail to distinguish passes of the same type.
     *
     * @param string $description
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->attributes['description'] = $description;

        return $this;
    }
    /**
     * Display name of the organization that originated and signed the pass.
     *
     * @param string $organizationName
     * @return self
     */
    public function setOrganizationName(string $organizationName): self
    {
        $this->attributes['organizationName'] = $organizationName;

        return $this;
    }
    /**
     * Pass type identifier, as issued by Apple. The value must correspond with
     * your signing certificate.
     *
     * @param string $passTypeIdentifier
     * @return self
     */
    public function setPassTypeIdentifier(string $passTypeIdentifier): self
    {
        $this->attributes['passTypeIdentifier'] = $passTypeIdentifier;

        return $this;
    }
    /**
     * Serial number that uniquely identifies the pass. No two passes with the same pass type identifier may have the same serial number.
     *
     * @param string $serialNumber
     * @return self
     */
    public function setSerialNumber(string $serialNumber): self
    {
        $this->attributes['serialNumber'] = $serialNumber;

        return $this;
    }
    /**
     * Team identifier of the organization that originated and signed the pass,
     * as issued by Apple.
     *
     * @param string $teamIdentifier
     * @return self
     */
    public function setTeamIdentifier(string $teamIdentifier): self
    {
        $this->attributes['teamIdentifier'] = $teamIdentifier;

        return $this;
    }
    /**
     * A URL to be passed to the associated app when launching it.
     * If this key is present, the associatedStoreIdentifiers key
     * must also be present.
     *
     * @param string $appLaunchURL
     * @param array $associatedStoreIdentifier A list of iTunes Store item identifiers for the associated apps.
     * @return self
     */
    public function setAppLaunchURL(string $appLaunchURL, array $associatedStoreIdentifier = null): self
    {
        $this->attributes['appLaunchURL'] = $appLaunchURL;
        if (is_array($associatedStoreIdentifier)) {
            $this->setAssociatedStoreIdentifier($associatedStoreIdentifier);
        }

        return $this;
    }
    /**
     *  A list of iTunes Store item identifiers for the associated apps.
     *
     * Only one item in the list is used—the first item identifier for an app
     * compatible with the current device. If the app is not installed, the
     * link opens the App Store and shows the app. If the app is already
     * installed, the link launches the app.
     *
     * @param array $associatedStoreIdentifier
     * @return self
     */
    public function setAssociatedStoreIdentifier(array $associatedStoreIdentifier): self
    {
        $this->attributes['associatedStoreIdentifiers'] = $associatedStoreIdentifier;

        return $this;
    }
    /**
     * Custom information for companion apps. This data is not displayed to the user.
     *
     * For example, a pass for a cafe could include information about the user’s
     * favorite drink and sandwich in a machine-readable form for the companion
     * app to read, making it easy to place an order for “the usual” from the app.
     *
     * Available in iOS 7.0.
     *
     * @param array $userInfo
     * @return self
     */
    public function setUserInfo(array $userInfo): self
    {
        $this->attributes['userInfo'] = $userInfo;

        return $this;
    }

    /**
     * Date and time when the pass expires.
     *
     * @param \Carbon\Carbon $expirationDate
     * @return self
     */
    public function setExpirationDate(Carbon $expirationDate): self
    {
        $this->attributes['expirationDate'] = $expirationDate;

        return $this;
    }

    /**
     * Indicates that the pass is void—for example, a one time use coupon that has
     * been redeemed. The default value is false.
     * Available in iOS 7.0.
     *
     * @param bool $flag
     * @return self
     */
    public function setVoided(bool $flag): self
    {
        $this->attributes['voided'] = $flag;

        return $this;
    }

    /**
     *  Beacons marking locations where the pass is relevant.
     *
     * @param \Illuminate\Support\Collection $beacons
     * @return self
     */
    public function setBeacons(Collection $beacons): self
    {
        $this->attributes['beacons'] = $beacons;
        return $this;
    }

    /**
     *  Beacon marking a location where the pass is relevant.
     *
     * @param \Thenextweb\Definitions\Dictionary\Beacon $beacon
     * @return self
     */
    public function addBeacon(Beacon $beacon): self
    {
        if (!array_key_exists('beacons', $this->attributes)) {
            $this->attributes['beacons'] = collect();
        }

        $this->attributes['beacons']->push($beacon);

        return $this;
    }

    /**
     * Locations where the pass is relevant. For example, the location of your store.
     *
     * @param \Illuminate\Support\Collection<\Thenextweb\Definitions\Dictionary\Location> $locations
     * @return self
     */
    public function setLocations(Collection $locations): self
    {
        $this->attributes['locations'] = $locations;
        return $this;
    }

    /**
     * Locations where the pass is relevant. For example, the location of your store.
     *
     * @param \Thenextweb\Definitions\Dictionary\Location $location
     * @return self
     */
    public function addLocation(Location $location): self
    {
        if (!array_key_exists('locations', $this->attributes)) {
            $this->attributes['locations'] = collect();
        }

        $this->attributes['locations']->push($location);

        return $this;
    }

    /**
     * Maximum distance in meters from a relevant latitude and longitude that
     * the pass is relevant. This number is compared to the pass’s default distance
     * and the smaller value is used.
     *
     * Available in iOS 7.0.
     *
     * @param int $maxDistance
     * @return self
     */
    public function setMaxDistance(int $maxDistance) : self
    {
        $this->attributes['maxDistance'] = $maxDistance;

        return $this;
    }

    /**
     * Recommended for event tickets and boarding passes; otherwise optional.
     * Date and time when the pass becomes relevant. For example, the start
     * time of a movie.
     *
     * @param \Carbon\Carbon $relevantDate
     * @return self
     */
    public function setRelevantDate(Carbon $relevantDate): self
    {
        $this->attributes['relevantDate'] = $relevantDate;

        return $this;
    }

    /**
     * Information specific to the pass’s barcode.
     *
     * For iOS 8 and earlier
     *
     * @param \Thenextweb\Definitions\Dictionary\Barcode $barcode
     * @deprecated Use addBarcode instead for iOS 9 and later
     */
    public function setBarcode(Barcode $barcode): self
    {
        $this->attributes['barcode'] = $barcode;

        return $this;
    }

    /**
     * Information specific to the pass’s barcode. The system uses the first
     * valid barcode dictionary in the array. Additional dictionaries can be
     * added as fallbacks.
     *
     * @param \Illuminate\Support\Collection<\Thenextweb\Definitions\Dictionary\Barcode> $barcodes
     * @return self
     */
    public function setBarcodes(Collection $barcodes): self
    {
        $this->attributes['barcodes'] = $barcodes;
        return $this;
    }

    /**
     * Information specific to the pass’s barcode.
     *
     * @param \Thenextweb\Definitions\Dictionary\Barcode $barcode
     * @return self
     */
    public function addBarcode(Barcode $barcode): self
    {
        if (!array_key_exists('barcodes', $this->attributes)) {
            $this->attributes['barcodes'] = collect();
        }

        $this->attributes['barcodes']->push($barcode);

        return $this;
    }

    /**
     * Background color of the pass, specified as an CSS-style RGB triple.
     * For example, rgb(23, 187, 82).
     *
     * @param string $color
     * @return self
     */
    public function setBackgroundColor(string $color) : self
    {
        $this->attributes['backgroundColor'] = $color;

        return $this;
    }

    /**
     * Foreground color of the pass, specified as a CSS-style RGB triple.
     * For example, rgb(100, 10, 110).
     *
     * @param string $foregroundColor
     * @return self
     */
    public function setForegroundColor(string $foregroundColor) : self
    {
        $this->attributes['foregroundColor'] = $foregroundColor;

        return $this;
    }

    /**
     *  Color of the label text, specified as a CSS-style RGB triple.
     * For example, rgb(255, 255, 255).
     *
     * If omitted, the label color is determined automatically.
     *
     * @param string $labelColor
     * @return self
     */
    public function setLabelColor(string $labelColor) : self
    {
        $this->attributes['labelColor'] = $labelColor;

        return $this;
    }

    /**
     * Text displayed next to the logo on the pass.
     *
     * @param string $logoText
     * @return self
     */
    public function setLogoText(string $logoText) : self
    {
        $this->attributes['logoText'] = $logoText;

        return $this;
    }

    /**
     * If true, the strip image is displayed without a shine effect.
     * The default value prior to iOS 7.0 is false.
     *
     * In iOS 7.0, a shine effect is never applied, and this key is deprecated.
     *
     * @param bool $flag
     * @return self
     */
    public function setSuppressStripShine(bool $flag) : self
    {
        $this->attributes['suppressStripShine'] = $flag;

        return $this;
    }

    /**
     * The URL of a web service that conforms to the API described in
     * PassKit Web Service Reference.
     *
     * The web service must use the HTTPS protocol; the leading https:// is
     * included in the value of this key.
     *
     * On devices configured for development, there is UI in Settings to allow
     * HTTP web services.
     *
     * @param string $webServiceURL
     * @param string $authenticationToken
     * @return self
     */
    public function setWebService(string $webServiceURL, string $authenticationToken) : self
    {
        $this->attributes['webServiceURL'] = $webServiceURL;
        $this->attributes['authenticationToken'] = $authenticationToken;

        return $this;
    }

    /**
     * Information used for Value Added Service Protocol transactions.
     *
     * @param \Thenextweb\Definitions\Dictionary\Nfc $nfc
     * @return self
     */
    public function setNfc(Nfc $nfc) : self
    {
        $this->attributes['nfc'] = $nfc;

        return $this;
    }

    /**
     * AUXILIARY FIELDS
     **/

    /**
     * Additional fields to be displayed on the front of the pass.
     *
     * @param Field $field
     * @return $this
     */
    public function appendAuxiliaryField(Field $field) : self
    {
        $this->getStructure('auxiliaryFields')->push($field);

        return $this;
    }

    /**
     * Additional fields to be displayed on the front of the pass.
     *
     * @param \Thenextweb\Definitions\Dictionary\Field $field
     * @return \Illuminate\Support\Collection
     */
    public function prependAuxiliaryField(Field $field) : Collection
    {
        return $this->getStructure('auxiliaryFields')->prepend($field);
    }

    /**
     * Additional fields to be displayed on the front of the pass.
     *
     * @param \Thenextweb\Definitions\Dictionary\Field $field
     * @return self
     */
    public function addAuxiliaryField(Field $field) : self
    {
        return $this->appendAuxiliaryField($field);
    }

    /**
     * Additional fields to be displayed on the front of the pass.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAuxiliaryFields() : Collection
    {
        return $this->getStructure('auxiliaryFields');
    }

    /**
     * BACK FIELDS
     */

    /**
     * Fields to be on the back of the pass.
     * @param Field $field
     * @return $this
     */
    public function appendBackField(Field $field) : self
    {
        $this->getStructure('backFields')->push($field);

        return $this;
    }

    /**
     * Fields to be on the back of the pass.
     *
     * @param \Thenextweb\Definitions\Dictionary\Field $field
     * @return \Illuminate\Support\Collection
     */
    public function prependBackField(Field $field) : Collection
    {
        return $this->getStructure('backFields')->prepend($field);
    }

    /**
     * Fields to be on the back of the pass.
     *
     * @param \Thenextweb\Definitions\Dictionary\Field $field
     * @return self
     */
    public function addBackField(Field $field) : self
    {
        $this->appendBackField($field);

        return $this;
    }

    /**
     * Fields to be on the back of the pass.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getBackFields() : Collection
    {
        return $this->getStructure('backFields');
    }

    /**
     * HEADER FIELDS
     */

    /**
     * Fields to be displayed in the header on the front of the pass.
     *
     * Use header fields sparingly; unlike all other fields, they remain visible
     * when a stack of passes are displayed.
     *
     * @param \Thenextweb\Definitions\Dictionary\Field $field
     * @return self
     */
    public function appendHeaderField(Field $field) : self
    {
        $this->getStructure('headerFields')->push($field);

        return $this;
    }

    /**
     * Fields to be displayed in the header on the front of the pass.
     *
     * Use header fields sparingly; unlike all other fields, they remain visible
     * when a stack of passes are displayed.
     *
     * @param \Thenextweb\Definitions\Dictionary\Field $field
     * @return \Illuminate\Support\Collection
     */
    public function prependHeaderField(Field $field) : Collection
    {
        return $this->getStructure('headerFields')->prepend($field);
    }

    /**
     * Fields to be displayed in the header on the front of the pass.
     *
     * Use header fields sparingly; unlike all other fields, they remain visible
     * when a stack of passes are displayed.
     *
     * @param \Thenextweb\Definitions\Dictionary\Field $field
     * @return self
     */
    public function addHeaderField(Field $field) : self
    {
        $this->appendHeaderField($field);

        return $this;
    }

    public function getHeaderFields() : Collection
    {
        return $this->getStructure('headerFields');
    }

    /**
     * PRIMARY FIELDS
     */

    /**
     * Fields to be displayed prominently on the front of the pass.
     *
     * @param \Thenextweb\Definitions\Dictionary\Field $field
     * @return self
     */
    public function appendPrimaryField(Field $field) : self
    {
        $this->getStructure('primaryFields')->push($field);

        return $this;
    }

    /**
     * Fields to be displayed prominently on the front of the pass.
     *
     * @param \Thenextweb\Definitions\Dictionary\Field $field
     * @return \Illuminate\Support\Collection
     */
    public function prependPrimaryField(Field $field) : Collection
    {
        return $this->getStructure('primaryFields')->prepend($field);
    }

    /**
     * Fields to be displayed prominently on the front of the pass.
     *
     * @param \Thenextweb\Definitions\Dictionary\Field $field
     * @return self
     */
    public function addPrimaryField(Field $field) : self
    {
        $this->appendPrimaryField($field);

        return $this;
    }

    public function getPrimaryFields() : Collection
    {
        return $this->getStructure('primaryFields');
    }

    /**
     * SECONDARY FIELDS
     */

    /**
     * Fields to be displayed on the front of the pass.
     *
     * @param \Thenextweb\Definitions\Dictionary\Field $field
     * @return self
     */
    public function appendSecondaryField(Field $field) : self
    {
        $this->getStructure('secondaryFields')->push($field);

        return $this;
    }

    /**
     * Fields to be displayed on the front of the pass.
     *
     * @param \Thenextweb\Definitions\Dictionary\Field $field
     * @return \Illuminate\Support\Collection
     */
    public function prependSecondaryField(Field $field) : Collection
    {
        return $this->getStructure('secondaryFields')->prepend($field);
    }

    /**
     * Fields to be displayed on the front of the pass.
     *
     * @param \Thenextweb\Definitions\Dictionary\Field $field
     * @return self
     */
    public function addSecondaryField(Field $field) : self
    {
        $this->appendSecondaryField($field);

        return $this;
    }

    public function getSecondaryFields() : Collection
    {
        return $this->getStructure('secondaryFields');
    }

    /**
     * @param string $structure
     * @return \Illuminate\Support\Collection
     */
    protected function getStructure($structure) : Collection
    {
        if (!array_key_exists('structure', $this->attributes)) {
            $this->attributes['structure'] = [];
        }

        if (!array_key_exists($structure, $this->attributes['structure'])) {
            $this->attributes['structure'][$structure] = collect();
        }

        return $this->attributes['structure'][$structure];
    }

    /**
     *
     * @throws \Illuminate\Validation\ValidationException
     * @return array
     */
    public function getPassDefinition() : array
    {
        $definition = $this->toArray();

        Validator::make($definition, $this->rules())->validate();

        return $definition;
    }

    /**
     * Returns an array representation of the definition compatible with PassKit Package Format
     */
    public function toArray() : array
    {
        $data = array_map(function ($value) {
            return $value instanceof Arrayable ? $value->toArray() : $value;
        }, $this->attributes);

        if (array_key_exists('userInfo', $data) && is_array($data['userInfo'])) {
            $data['userInfo'] = with(new Fluent($data['userInfo']))->toJson();
        }

        if (array_key_exists('expirationDate', $data) && $data['expirationDate'] instanceof Carbon) {
            $data['expirationDate'] = $data['expirationDate']->format(DATE_W3C);
        }

        if (array_key_exists('relevantDate', $data) && $data['relevantDate'] instanceof Carbon) {
            $data['relevantDate'] = $data['relevantDate']->format(DATE_W3C);
        }

        if (array_key_exists('structure', $data)) {
            $structure = $data['structure'];
            unset($data['structure']);

            foreach ($structure as $key => $value) {
                if (!$value instanceof Collection) {
                    continue;
                }

                $structure[$key] = $value->toArray();
            }

            $data[$this->style] = $structure;
        }

        return $data;
    }

    public function rules() : array
    {
        return [
            'description' => 'required',
            'formatVersion' => 'required',
            'organizationName' => 'required',
            'passTypeIdentifier' => 'required',
            'serialNumber' => 'required',
            'teamIdentifier' => 'required',

            //'appLaunchURL'
            'associatedStoreIdentifiers' => 'required_with:appLaunchURL',

            //'userInfo',

            //'expirationDate,
            //'voided',

            'beacons' => 'sometimes',
            //'beacons.*.major',
            //'beacons.*.minor',
            'beacons.*.proximityUUID' => 'required',
            //'beacons.*.relevantText'

            'locations' => 'sometimes',
            //'locations.*.altitude',
            'locations.*.latitude' => 'required',
            'locations.*.longitude' => 'required',
            //'locations.*.relevantText',

            //'maxDistance',
            //'relevantDate',

            'boardingPass' => 'sometimes',
            'boardingPass.transitType' => 'required_with:boardingPass',
            'boardingPass.auxiliaryFields.*.key' => 'required',
            'boardingPass.auxiliaryFields.*.value' => 'required',
            'boardingPass.backFields.*.key' => 'required',
            'boardingPass.backFields.*.value' => 'required',
            'boardingPass.headerFields.*.key' => 'required',
            'boardingPass.headerFields.*.value' => 'required',
            'boardingPass.primaryFields.*.key' => 'required',
            'boardingPass.primaryFields.*.value' => 'required',
            'boardingPass.secondaryFields.*.key' => 'required',
            'boardingPass.secondaryFields.*.value' => 'required',

            'coupon' => 'sometimes',
            'coupon.auxiliaryFields.*.key' => 'required',
            'coupon.auxiliaryFields.*.value' => 'required',
            'coupon.backFields.*.key' => 'required',
            'coupon.backFields.*.value' => 'required',
            'coupon.headerFields.*.key' => 'required',
            'coupon.headerFields.*.value' => 'required',
            'coupon.primaryFields.*.key' => 'required',
            'coupon.primaryFields.*.value' => 'required',
            'coupon.secondaryFields.*.key' => 'required',
            'coupon.secondaryFields.*.value' => 'required',

            'eventTicket' => 'sometimes',
            'eventTicket.auxiliaryFields.*.key' => 'required',
            'eventTicket.auxiliaryFields.*.value' => 'required',
            'eventTicket.backFields.*.key' => 'required',
            'eventTicket.backFields.*.value' => 'required',
            'eventTicket.headerFields.*.key' => 'required',
            'eventTicket.headerFields.*.value' => 'required',
            'eventTicket.primaryFields.*.key' => 'required',
            'eventTicket.primaryFields.*.value' => 'required',
            'eventTicket.secondaryFields.*.key' => 'required',
            'eventTicket.secondaryFields.*.value' => 'required',

            'generic' => 'sometimes',
            'generic.auxiliaryFields.*.key' => 'required',
            'generic.auxiliaryFields.*.value' => 'required',
            'generic.backFields.*.key' => 'required',
            'generic.backFields.*.value' => 'required',
            'generic.headerFields.*.key' => 'required',
            'generic.headerFields.*.value' => 'required',
            'generic.primaryFields.*.key' => 'required',
            'generic.primaryFields.*.value' => 'required',
            'generic.secondaryFields.*.key' => 'required',
            'generic.secondaryFields.*.value' => 'required',

            'storeCard' => 'sometimes',
            'storeCard.auxiliaryFields.*.key' => 'required',
            'storeCard.auxiliaryFields.*.value' => 'required',
            'storeCard.backFields.*.key' => 'required',
            'storeCard.backFields.*.value' => 'required',
            'storeCard.headerFields.*.key' => 'required',
            'storeCard.headerFields.*.value' => 'required',
            'storeCard.primaryFields.*.key' => 'required',
            'storeCard.primaryFields.*.value' => 'required',
            'storeCard.secondaryFields.*.key' => 'required',
            'storeCard.secondaryFields.*.value' => 'required',

            'barcode' => 'sometimes',
            //'barcode.altText',
            'barcode.format' => 'required_with:barcode',
            'barcode.message' => 'require_with:barcode',
            'barcode.messageEncoding' => 'required_with:barcode',

            'barcodes' => 'sometimes|array',
            //'barcodes.*.altText',
            'barcodes.*.format' => 'required',
            'barcodes.*.message' => 'required',
            'barcodes.*.messageEncoding' => 'required',

            //'backgroundColor',
            //'foregroundColor',
            //'groupingIdentifier',
            //'labelColor',
            //'logoText',
            //'suppressStripShine',

            //'webServiceURL',
            'authenticationToken' => 'required_with:webServiceURL|min:16',

            'nfc' => 'sometimes',
            'nfc.message' => 'required_with:nfc',
            //'nfc.encryptionPublicKey',
        ];
    }
}
