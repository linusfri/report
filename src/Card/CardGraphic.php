<?php

namespace App\Card;

class CardGraphic extends Card implements \JsonSerializable
{
    private string $utf8Representation;

    public function __construct(int $value, string $suit, string $utf8Representation)
    {
        parent::__construct($value, $suit);
        $this->utf8Representation = $utf8Representation;
    }

    public function getUtf8Rep(): string
    {
        return $this->utf8Representation;
    }

    /**
     * jsonSerialize.
     *
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        $utf8 = $this->getUtf8Rep();

        return [
            'suit' => $this->getSuit(),
            'value' => $this->getValue(),
            'utf8' => "$utf8",
        ];
    }
}
