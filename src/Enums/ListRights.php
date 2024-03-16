<?php

namespace Enums;

enum ListRights: string
{
    /**
     * Listing possible group rights.
     */
    case Send = 'send_messages';
    case Service = 'service_api';
    case Debug = 'debug';
}
