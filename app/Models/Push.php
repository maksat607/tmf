<?php

namespace App\Models;

class Push
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string|null
     */
    private $subtitle;

    /**
     * @var string|null
     */
    private $text;

    /**
     * @var string
     */
    private $category;

    /**
     * @var bool
     */
    private $increaseBadge = true;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var array
     */
    private $existsTags = [];

    /**
     * @var float|null
     */
    private $latitude;

    /**
     * @var float|null
     */
    private $longitude;

    /**
     * @var string|null
     */
    private $oneSignalPlayerId;

    /**
     * @param string $title
     * @param string $category
     */
    public function __construct(string $title, string $category)
    {
        $this->title = $title;
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return null|string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param null|string $subtitle
     * @return $this
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * @return string|string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIncreaseBadge(): bool
    {
        return $this->increaseBadge;
    }

    /**
     * @param bool $increaseBadge
     * @return $this
     */
    public function setIncreaseBadge(bool $increaseBadge)
    {
        $this->increaseBadge = $increaseBadge;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOneSignalPlayerId()
    {
        return $this->oneSignalPlayerId;
    }

    /**
     * @param string|null $oneSignalPlayerId
     * @return $this
     */
    public function setOneSignalPlayerId($oneSignalPlayerId)
    {
        $this->oneSignalPlayerId = $oneSignalPlayerId;

        return $this;
    }

    /**
     * @return array
     */
    public function getExistsTags()
    {
        return $this->existsTags;
    }

    /**
     * @param array $existsTags
     * @return $this
     */
    public function setExistsTags($existsTags)
    {
        $this->existsTags = $existsTags;

        return $this;
    }

    /**
     * @param $latitude
     * @param $longitude
     * @return $this
     */
    public function setCoordinates($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return float|null
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'text' => $this->text,
            'category' => $this->category,
            'increaseBadge' => $this->increaseBadge,
            'parameters' => $this->parameters,
            'oneSignalPlayerId' => $this->oneSignalPlayerId
        ];
    }
}
