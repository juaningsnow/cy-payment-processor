<?php

namespace BaseCode\Common\Traits;

trait HasAudits
{
    public function getLatestAudit()
    {
        return $this->latestAudit->first();
    }

    public function getAudits()
    {
        return $this->audits;
    }

    public function latestAudit()
    {
        return $this->audits()->latest();
    }
}
