<?php namespace NurgisaA\CommerceML\Model;

use NurgisaA\CommerceML\ORM\Model;

class Product extends Model
{
    /**
     * @var string $id
     */
    public $id;

    /**
     * @var string $name
     */
    public $name;

    /**
     * @var string $sku
     */
    public $sku;

    /**
     * @var string $unit
     */
    public $unit;

    /**
     * @var string $status
     */
    public $status;

    /**
     * @var string $description
     */
    public $description;

    /**
     * @var int $quantity
     */
    public $quantity;

    /**
     * @var array $price
     */
    public $price = [];

    /**
     * @var array $categories
     */
    public $categories = [];

    /**
     * @var array $requisites
     */
    public $requisites = [];

    /**
     * @var array $properties
     */
    public $properties = [];

    /**
     * @var array $specification
     */
    public $specification = [];

    /**
     * @var array $storage
     */
    public $storage = [];
    /**
     * @var array $image
     */
    public $image = [];
    /**
     * @var string
     */
    public $vendor_code;
    /**
     * @var string
     */
    public $merchant_code;


    /**
     * Class constructor.
     *
     * @param string [$importXml]
     * @param string [$offersXml]
     */
    public function __construct($importXml = null, $offersXml = null)
    {
        $this->name = '';
        $this->quantity = 0;
        $this->description = '';


        if (!is_null($importXml)) {
            $this->loadImport($importXml);
        }

        if (!is_null($offersXml)) {
            $this->loadOffers($offersXml);
        }
    }

    /**
     * Load primary data from import.xml.
     *
     * @param \SimpleXMLElement $xml
     *
     * @return void
     */
    public function loadImport($xml)
    {
        $this->id = trim($xml->Ид);

        $this->name = trim($xml->Наименование);
        $this->description = trim($xml->Описание);
//        $this->image = trim($xml->Картинка);

        $this->sku = trim($xml->ШтрихКод);
        $this->merchant_code = trim($xml->КодНоменклатуры);
        $this->vendor_code = trim($xml->Артикул);
        $this->unit = trim($xml->БазоваяЕдиница);

        $this->status = ($xml->Статус) ? trim($xml->Статус) : '';


        if ($xml->Группы) {
            foreach ($xml->Группы->Ид as $categoryId) {
                $this->categories[] = (string)$categoryId;
            }
        }

        if ($xml->ЗначенияРеквизитов) {
            foreach ($xml->ЗначенияРеквизитов->ЗначениеРеквизита as $value) {
                $name = (string)$value->Наименование;
                $this->requisites[$name] = (string)$value->Значение;
            }
        }
        if ($xml->Картинка) {
            foreach ($xml->Картинка as $value) {
                $this->image[] = (string)$value;
            }
        }
        if ($xml->ХарактеристикиТовара) {
            foreach ($xml->ХарактеристикиТовара->ХарактеристикаТовара as $value) {


                $name = (string)$value->Наименование;
                $this->specification[$name]['value'] = (string)$value->Значение;
                $this->specification[$name]['is_filter'] = ((string)$value->ДляФильтра == "true") ? true : false;
            }
        }
        if ($xml->ЗначенияСвойств) {
            foreach ($xml->ЗначенияСвойств->ЗначенияСвойства as $prop) {

                $id = (string)$prop->Ид;
                $value = (string)$prop->Значение;

                if ($value) {
                    $this->properties[$id] = $value;
                }
            }
        }


    }

    /**
     * Load primary data form offers.xml.
     *
     * @param \SimpleXMLElement $xml
     *
     * @return void
     */
    public function loadOffers($xml)
    {
        if ($xml->Количество) {
            $this->quantity = (int)$xml->Количество;
        }

        if ($xml->Цены) {
            foreach ($xml->Цены->Цена as $price) {
                $id = (string)$price->ИдТипаЦены;

                $this->price[$id] = [
                    'type' => $id,
                    'currency' => (string)$price->Валюта,
                    'value' => (float)$price->ЦенаЗаЕдиницу
                ];
            }
        }
        if ($xml->КоличествоПомагазинам->Остаток) {
            foreach ($xml->КоличествоПомагазинам->Остаток as $item) {
                $id = (string)$item->ИдМагазина;


                $this->storage[$id] = [
                    'name' => (string)$item->Наименование,
                    'address' => (string)$item->АдресМагазина,
                    'value' => (int)$item->Количество
                ];
            }
        }
//        if ($xml->Склад) {
//            foreach ($xml->Склад as $storage) {
//                $id = (string)$storage['ИдСклада'];
//                $this->storage[$id] = (string)$storage['КоличествоНаСкладе'];
//            }
//        }
    }

    /**
     * Get price by type.
     *
     * @param string $type
     *
     * @return float
     */
    public function getPrice($type)
    {
        foreach ($this->price as $price) {
            if ($price['type'] == $type) {
                return $price['value'];
            }
        }

        return 0;
    }
}
