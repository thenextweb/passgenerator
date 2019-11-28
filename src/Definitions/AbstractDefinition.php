<?php

namespace Thenextweb\Definitions;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Illuminate\Validation\Factory as Validator;
use Illuminate\Validation\ValidationException;
use Thenextweb\Definitions\Dictionary\Beacon;
use Thenextweb\Definitions\Dictionary\Field;
use Thenextweb\Definitions\Dictionary\Location;
use Thenextweb\Definitions\Dictionary\Barcode;
use Thenextweb\Definitions\Dictionary\Nfc;

abstract class AbstractDefinition extends Fluent implements DefinitionInterface
{
    const TRANSIT_TYPE_AIR = 'PKTransitTypeAir';
    const TRANSIT_TYPE_BOAT = 'PKTransitTypeBoat';
    const TRANSIT_TYPE_BUS = 'PKTransitTypeBus';
    const TRANSIT_TYPE_GENERIC = 'PKTransitTypeGeneric';
    const TRANSIT_TYPE_TRAIN = 'PKTransitTypeTrain';

    protected $pass = [];

    protected $formatVersion = 1;

    /**
     * @var Validator
     */
    protected $validator;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        $this->validator = app('validator');

        $this->attributes['formatVersion'] = $this->formatVersion;

        $this->attributes['passTypeIdentifier'] = config('passgenerator.pass_type_identifier', '');
        $this->attributes['organizationName']   = config('passgenerator.organization_name', '');
        $this->attributes['teamIdentifier']     = config('passgenerator.team_identifier', '');
    }

    public function setDescription($description)
    {
        $this->attributes['description'] = $description;

        return $this;
    }

    public function setOrganizationName($organizationName)
    {
        $this->attributes['organizationName'] = $organizationName;

        return $this;
    }

    public function setPassTypeIdentifier($passTypeIdentifier)
    {
        $this->attributes['passTypeIdentifier'] = $passTypeIdentifier;

        return $this;
    }

    public function setSerialNumber($serialNumber)
    {
        $this->attributes['serialNumber'] = $serialNumber;

        return $this;
    }

    public function setTeamIdentifier($teamIdentifier)
    {
        $this->attributes['teamIdentifier'] = $teamIdentifier;

        return $this;
    }

    public function setAppLaunchURL($appLaunchURL, $associatedStoreIdentifier)
    {
        $this->attributes['appLaunchURL'] = $appLaunchURL;
        $this->setAssociatedStoreIdentifier($associatedStoreIdentifier);

        return $this;
    }

    public function setAssociatedStoreIdentifier($associatedStoreIdentifier)
    {
        $this->attributes['associatedStoreIdentifiers'] = is_array($associatedStoreIdentifier)
            ? $associatedStoreIdentifier
            : [$associatedStoreIdentifier];

        return $this;
    }

    public function setUserInfo(array $userInfo)
    {
        $this->attributes['userInfo'] = $userInfo;

        return $this;
    }

    public function setExpirationDate(Carbon $expirationDate)
    {
        $this->attributes['expirationDate'] = $expirationDate;

        return $this;
    }

    public function setVoided(bool $flag)
    {
        $this->attributes['voided'] = $flag;

        return $this;
    }

    public function setBeacons($beacons)
    {
        if ($beacons instanceof Collection) {
            $this->attributes['beacons'] = $beacons;
        } elseif ($beacons instanceof Beacon) {
            $this->attributes['beacons'] = collect([$beacons]);
        } else {
            throw new \BadMethodCallException('Argument for setBeacons must be a Beacon object or a collection of Beacon objects');
        }
    }

    public function addBeacon(Beacon $beacon)
    {
        if (!array_key_exists('beacons', $this->attributes)) {
            $this->attributes['beacons'] = collect();
        }

        $this->attributes['beacons']->push($beacon);

        return $this;
    }

    public function setLocations($locations)
    {
        if ($locations instanceof Collection) {
            $this->attributes['locations'] = $locations;
        } elseif ($locations instanceof Location) {
            $this->attributes['locations'] = collect([$locations]);
        } else {
            throw new \BadMethodCallException('Argument for setLocations must be a Location object or a collection of Location objects');
        }
    }

    public function addLocation(Location $location)
    {
        if (!array_key_exists('locations', $this->attributes)) {
            $this->attributes['locations'] = collect();
        }

        $this->attributes['locations']->push($location);

        return $this;
    }

    public function setMaxDistance($maxDistance)
    {
        $this->attributes['maxDistance'] = $maxDistance;

        return $this;
    }

    public function setRelevantDate(Carbon $relevantDate)
    {
        $this->attributes['relevantDate'] = $relevantDate;

        return $this;
    }

    /**
     * For iOS 8 and earlier
     *
     * @param Barcode $barcode
     * @deprecated Use addBarcode instead for iOS 9 and later
     */
    public function setBarcode(Barcode $barcode)
    {
        $this->attributes['barcode'] = $barcode;
    }

    public function setBarcodes($barcodes)
    {
        if ($barcodes instanceof Collection) {
            $this->attributes['barcodes'] = $barcodes;
        } elseif ($barcodes instanceof Barcode) {
            $this->attributes['barcodes'] = collect([$barcodes]);
        } else {
            throw new \BadMethodCallException('Argument for setBarcodes must be a Barcode object or a collection of Barcode objects');
        }
    }

    public function addBarcode(Barcode $barcode)
    {
        if (!array_key_exists('barcodes', $this->attributes)) {
            $this->attributes['barcodes'] = collect();
        }

        $this->attributes['barcodes']->push($barcode);

        return $this;
    }

    public function setBackgroundColor($color)
    {
        $this->attributes['backgroundColor'] = $color;

        return $this;
    }

    public function setForegroundColor($foregroundColor)
    {
        $this->attributes['foregroundColor'] = $foregroundColor;

        return $this;
    }

    /**
     * Valid for Event Tickets and Boarding Passes ONLY
     *
     * @param $groupingIdentifier
     * @return $this
     */
    public function setGroupingIdentifier($groupingIdentifier)
    {
        $this->attributes['groupingIdentifier'] = $groupingIdentifier;

        return $this;
    }

    public function setLabelColor($labelColor)
    {
        $this->attributes['labelColor'] = $labelColor;

        return $this;
    }

    public function setLogoText($logoText)
    {
        $this->attributes['logoText'] = $logoText;

        return $this;
    }

    public function setSuppressStripShine(bool $flag)
    {
        $this->attributes['suppressStripShine'] = $flag;

        return $this;
    }

    public function setWebService($webServiceURL, $authenticationToken)
    {
        $this->attributes['authenticationToken'] = $authenticationToken;
        $this->attributes['webServiceURL'] = $webServiceURL;

        return $this;
    }

    public function setNfc(Nfc $nfc)
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
    public function appendAuxiliaryField(Field $field)
    {
        $this->getStructure('auxiliaryFields')->push($field);

        return $this;
    }

    public function prependAuxiliaryField(Field $field)
    {
        return $this->getStructure('auxiliaryFields')->prepend($field);
    }

    public function addAuxiliaryField(Field $field)
    {
        return $this->appendAuxiliaryField($field);
    }

    public function getAuxiliaryFields()
    {
        return $this->getStructure('auxiliaryFields');
    }

    /* ######### */
    /* AUXILIARY FIELDS */

    /**
     * @param Field $field
     * @return $this
     */
    public function appendBackField(Field $field)
    {
        $this->getStructure('backFields')->push($field);

        return $this;
    }

    public function prependBackField(Field $field)
    {
        return $this->getStructure('backFields')->prepend($field);
    }

    public function addBackField(Field $field)
    {
        $this->appendBackField($field);

        return $this;
    }

    public function getBackFields()
    {
        return $this->getStructure('backFields');
    }

    /* ############### */
    /* HEADER FIELDS */

    public function appendHeaderField(Field $field)
    {
        $this->getStructure('headerFields')->push($field);

        return $this;
    }

    public function prependHeaderField(Field $field)
    {
        return $this->getStructure('headerFields')->prepend($field);
    }

    public function addHeaderField(Field $field)
    {
        $this->appendHeaderField($field);

        return $this;
    }

    public function getHeaderFields()
    {
        return $this->getStructure('headerFields');
    }

    /* ############### */
    /* PRIMARY FIELDS */

    public function appendPrimaryField(Field $field)
    {
        $this->getStructure('primaryFields')->push($field);

        return $this;
    }

    public function prependPrimaryField(Field $field)
    {
        return $this->getStructure('primaryFields')->prepend($field);
    }

    public function addPrimaryField(Field $field)
    {
        $this->appendPrimaryField($field);

        return $this;
    }

    public function getPrimaryFields()
    {
        return $this->getStructure('primaryFields');
    }

    /* ############### */
    /* SECONDARY FIELDS */

    public function appendSecondaryField(Field $field)
    {
        $this->getStructure('secondaryFields')->push($field);

        return $this;
    }

    public function prependSecondaryField(Field $field)
    {
        return $this->getStructure('secondaryFields')->prepend($field);
    }

    public function addSecondaryField(Field $field)
    {
        $this->appendSecondaryField($field);

        return $this;
    }

    public function getSecondaryFields()
    {
        return $this->getStructure('secondaryFields');
    }

    public function setTransitType($transitType)
    {
        $this->attributes['transitType'] = $transitType;

        return $this;
    }

    /**
     * @param $structure
     * @return mixed
     */
    protected function getStructure($structure)
    {
        if (!array_key_exists('structure', $this->attributes)) {
            $this->attributes['structure'] = [];
        }

        if (!array_key_exists($structure, $this->attributes['structure'])) {
            $this->attributes['structure'][$structure] = collect();
        }

        return $this->attributes['structure'][$structure];
    }

    public function getPassDefinition()
    {
        $array = $this->toArray();

        $this->validate($array);

        return $array;
    }

    /**
     * Returns an array representation of the definition compatible with PassKit Package Format
     */
    public function toArray()
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

    protected function validate($data)
    {
        try {
            $this->validator->make($data, $this->rules())->validate();
        } catch (ValidationException $e) {
            dd($e->validator->getMessageBag()->toArray());
        }
    }

    public function rules()
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
