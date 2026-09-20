<?php

namespace Modules\Core\Enums;

enum RegexPattern: string
{
    case PersianWord = '/^[\x{600}-\x{6FF}\x{200c}\x{064b}\x{064d}\x{064c}\x{064e}\x{064f}\x{0650}\x{0651}\s]+$/u';
    case Website = '/^(https?:\/\/)?(www\.)[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,4}\b([-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)|(https?:\/\/)?(www\.)?(?!ww)[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,4}\b([-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)$/i';
    case OfficePhone = '/^0[1-8]{2}[0-9]{1,9}$/';
    case FaxNumber = '/^0[1-8]{2}\d{8}$/';
    case OnlyNumber = '/^\d*$/';
    case OnlyNonDigit = '/^\D*$/';
    case MobileNumber = '/^(09\d)[0-9]{8}$/';
    case Email = '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
    case HAS_PERSIAN_CHAR = '/[ء-ی]/u';
    case ENGLISH_WORD = '/^[A-Za-z\d\s]+$/';
    case DELIMITED_IDS = '/^(\d+_)*\d+$/';
}

