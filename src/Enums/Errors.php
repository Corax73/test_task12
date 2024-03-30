<?php

namespace Enums;

enum Errors: string
{
    /**
     * List of errors.
     */
    case NotFound = 'not found';
    case IncompleteData = 'incomplete data in the request';
    case Credentials = 'invalid credentials';
    case ConfirmPassword = 'password and password confirmation do not match';
    case Unique = 'not unique';
    case BadPassword = 'invalid characters in the password or shorter than 8 characters';
    case NoRights = 'no rights';
    case Default = 'error, try later';
    case AlreadyAvailable = 'already available';
    case AlreadyBlocked = 'already blocked';
}
