<?php
namespace AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\ConversionRate;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

/**
 * Class ConversionRateDetail
 *
 * Detail of conversion rate of First Monetary Unit.
 *
 * @package AmadeusService\Search\Entity\MasterPricerTravelBoardSearchReply\ConversionRate
 */
class ConversionRateDetail
{
    /**
     * To specify if code 6345 is a currency code or a city code. Default is a currency code.
     *
     * @Type("string")
     * @SerializedName("conversionType")
     * @var string
     */
    protected $conversionType;

    /**
     * Use ISO 4217 three alpha code to specify currency
     *
     * @Type("string")
     * @SerializedName("currency")
     * @var string
     */
    protected $currency;

    /**
     * @Type("string")
     * @SerializedName("rate")
     * @var string
     */
    protected $rate;

    /**
     * @Type("string")
     * @SerializedName("convertedAmountLink")
     * @var string
     */
    protected $convertedAmountLink;

    /**
     * ISO country code or Tax designator code
     *
     * @Type("string")
     * @SerializedName("taxQualifier")
     * @var string
     */
    protected $taxQualifier;

    /**
     * @return string
     */
    public function getConversionType()
    {
        return $this->conversionType;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @return string
     */
    public function getConvertedAmountLink()
    {
        return $this->convertedAmountLink;
    }

    /**
     * @return string
     */
    public function getTaxQualifier()
    {
        return $this->taxQualifier;
    }
}