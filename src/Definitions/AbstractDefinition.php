<?php

namespace Thenextweb\Definitions;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Fluent;
use Thenextweb\Definitions\Dictionary\Beacon;
use Thenextweb\Definitions\Dictionary\Field;
use Thenextweb\Definitions\Dictionary\Location;
use Thenextweb\Definitions\Dictionary\Barcode;
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
        parent::__construct($attributes);

        $this->attributes['formatVersion'] = $this->formatVersion;

        $this->attributes['passTypeIdentifier'] = config('passgenerator.pass_type_identifier', '');
        $this->attributes['organizationName']   = config('passgenerator.organization_name', '');
        $this->attributes['teamIdentifier']     = config('passgenerator.team_identifier', '');
    }

    public function setDescription(string $description) : self
    {
        $this->attributes['description'] = $description;

        return $this;
    }

    public function setOrganizationName(string $organizationName) : self
    {
        $this->attributes['organizationName'] = $organizationName;

        return $this;
    }

    public function setPassTypeIdentifier(string $passTypeIdentifier) : self
    {
        $this->attributes['passTypeIdentifier'] = $passTypeIdentifier;

        return $this;
    }

    public function setSerialNumber(string $serialNumber) : self
    {
        $this->attributes['serialNumber'] = $serialNumber;

        return $this;
    }

    public function setTeamIdentifier(string $teamIdentifier) : self
    {
        $this->attributes['teamIdentifier'] = $teamIdentifier;

        return $this;
    }

    public function setAppLaunchURL(string $appLaunchURL, array $associatedStoreIdentifier = null) : self
    {
        $this->attributes['appLaunchURL'] = $appLaunchURL;
        if (is_array($associatedStoreIdentifier)) {
            $this->setAssociatedStoreIdentifier($associatedStoreIdentifier);
        }

        return $this;
    }

    public function setAssociatedStoreIdentifier(array $associatedStoreIdentifier) : self
    {
        $this->attributes['associatedStoreIdentifiers'] = $associatedStoreIdentifier;

        return $this;
    }

    public function setUserInfo(array $userInfo) : self
    {
        $this->attributes['userInfo'] = $userInfo;

        return $this;
    }

    public function setExpirationDate(Carbon $expirationDate) : self
    {
        $this->attributes['expirationDate'] = $expirationDate;

        return $this;
    }

    public function setVoided(bool $flag) : self
    {
        $this->attributes['voided'] = $flag;

        return $this;
    }

    public function setBeacons(Collection $beacons) : self
    {
        $this->attributes['beacons'] = $beacons;
        return $this;
    }

    public function addBeacon(Beacon $beacon) : self
    {
        if (!array_key_exists('beacons', $this->attributes)) {
            $this->attributes['beacons'] = collect();
        }

        $this->attributes['beacons']->push($beacon);

        return $this;
    }

    public function setLocations(Collection $locations) : self
    {
        $this->attributes['locations'] = $locations;
        return $this;
    }

    public function addLocation(Location $location) : self
    {
        if (!array_key_exists('locations', $this->attributes)) {
            $this->attributes['locations'] = collect();
        }

        $this->attributes['locations']->push($location);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param int $maxDistance
     * @return self
     */
    public function setMaxDistance(int $maxDistance) : self
    {
        $this->attributes['maxDistance'] = $maxDistance;

        return $this;
    }

    public function setRelevantDate(Carbon $relevantDate) : self
    {
        $this->attributes['relevantDate'] = $relevantDate;

        return $this;
    }

    /**
     * For iOS 8 and earlier
     *
     * @param \Thenextweb\Definitions\Dictionary\Barcode $barcode
     * @deprecated Use addBarcode instead for iOS 9 and later
     */
    public function setBarcode(Barcode $barcode) : self
    {
        $this->attributes['barcode'] = $barcode;

        return $this;
    }

    public function setBarcodes(Collection $barcodes) : self
    {
        $this->attributes['barcodes'] = $barcodes;
        return $this;
    }

    public function addBarcode(Barcode $barcode) : self
    {
        if (!array_key_exists('barcodes', $this->attributes)) {
            $this->attributes['barcodes'] = collect();
        }

        $this->attributes['barcodes']->push($barcode);

        return $this;
    }

    public function setBackgroundColor(string $color) : self
    {
        $this->attributes['backgroundColor'] = $color;

        return $this;
    }

    public function setForegroundColor(string $foregroundColor) : self
    {
        $this->attributes['foregroundColor'] = $foregroundColor;

        return $this;
    }

    /**
     * Valid for Event Tickets and Boarding Passes ONLY
     *
     * @param string $groupingIdentifier
     * @return self
     */
    public function setGroupingIdentifier(string $groupingIdentifier) : self
    {
        $this->attributes['groupingIdentifier'] = $groupingIdentifier;

        return $this;
    }

    public function setLabelColor(string $labelColor) : self
    {
        $this->attributes['labelColor'] = $labelColor;

        return $this;
    }

    public function setLogoText(string $logoText) : self
    {
        $this->attributes['logoText'] = $logoText;

        return $this;
    }

    public function setSuppressStripShine(bool $flag) : self
    {
        $this->attributes['suppressStripShine'] = $flag;

        return $this;
    }

    public function setWebService(string $webServiceURL, string $authenticationToken) : self
    {
        $this->attributes['webServiceURL'] = $webServiceURL;
        $this->attributes['authenticationToken'] = $authenticationToken;

        return $this;
    }

    public function setNfc(Nfc $nfc) : self
    {
        $this->attributes['nfc'] = $nfc;

        return $this;
    }

    /* ######### */
    /* AUXILIARY FIELDS */

    /**
     * @param Field $field
     * @return $this
     */
    public function appendAuxiliaryField(Field $field) : self
    {
        $this->getStructure('auxiliaryFields')->push($field);

        return $this;
    }

    public function prependAuxiliaryField(Field $field) : Collection
    {
        return $this->getStructure('auxiliaryFields')->prepend($field);
    }

    public function addAuxiliaryField(Field $field) : self
    {
        return $this->appendAuxiliaryField($field);
    }

    public function getAuxiliaryFields() : Collection
    {
        return $this->getStructure('auxiliaryFields');
    }

    /* ######### */
    /* AUXILIARY FIELDS */

    /**
     * @param Field $field
     * @return $this
     */
    public function appendBackField(Field $field) : self
    {
        $this->getStructure('backFields')->push($field);

        return $this;
    }

    public function prependBackField(Field $field) : Collection
    {
        return $this->getStructure('backFields')->prepend($field);
    }

    public function addBackField(Field $field) : self
    {
        $this->appendBackField($field);

        return $this;
    }

    public function getBackFields() : Collection
    {
        return $this->getStructure('backFields');
    }

    /* ############### */
    /* HEADER FIELDS */

    public function appendHeaderField(Field $field) : self
    {
        $this->getStructure('headerFields')->push($field);

        return $this;
    }

    public function prependHeaderField(Field $field) : Collection
    {
        return $this->getStructure('headerFields')->prepend($field);
    }

    public function addHeaderField(Field $field) : self
    {
        $this->appendHeaderField($field);

        return $this;
    }

    public function getHeaderFields() : Collection
    {
        return $this->getStructure('headerFields');
    }

    /* ############### */
    /* PRIMARY FIELDS */

    public function appendPrimaryField(Field $field) : self
    {
        $this->getStructure('primaryFields')->push($field);

        return $this;
    }

    public function prependPrimaryField(Field $field) : Collection
    {
        return $this->getStructure('primaryFields')->prepend($field);
    }

    public function addPrimaryField(Field $field) : self
    {
        $this->appendPrimaryField($field);

        return $this;
    }

    public function getPrimaryFields() : Collection
    {
        return $this->getStructure('primaryFields');
    }

    /* ############### */
    /* SECONDARY FIELDS */

    public function appendSecondaryField(Field $field) : self
    {
        $this->getStructure('secondaryFields')->push($field);

        return $this;
    }

    public function prependSecondaryField(Field $field) : Collection
    {
        return $this->getStructure('secondaryFields')->prepend($field);
    }

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
