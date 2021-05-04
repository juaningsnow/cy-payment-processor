<?php

namespace BaseCode\Common\Traits;

trait HasTimestamps
{
    public static $timezone = 'Asia/Manila';

    public function getLocalCreatedAt()
    {
        return $this->getCreatedAt()->setTimezone(self::$timezone);
    }

    public function getCreatedAt()
    {
        return $this->created_at; // carbon instance
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function getCreatedAtDayDateTimeString()
    {
        return $this->getLocalCreatedAt()->toDayDateTimeString();
    }

    public function getCreatedAtFormattedDateString()
    {
        return $this->getLocalCreatedAt()->toFormattedDateString();
    }
}
