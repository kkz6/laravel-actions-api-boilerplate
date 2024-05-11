<?php

namespace Library\Actions;

class ActionManager
{
    /**
     * If flash messages are globally disabled
     */
    protected bool $flashMessageDisabledStatus = false;

    /**
     * Handle disabling flash messages
     */
    public function disableFlashMessages(bool $disabled = true): static
    {
        $this->flashMessageDisabledStatus = $disabled;

        return $this;
    }

    /**
     * If flash messages are disabled
     */
    public function flashMessagesDisabled(): bool
    {
        return $this->flashMessageDisabledStatus;
    }
}
