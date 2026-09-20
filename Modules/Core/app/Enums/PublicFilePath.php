<?php

namespace Modules\Core\Enums;

enum PublicFilePath: string
{
    case Logo = '/images/logo';
    case Default = '/images/default';
    case SocialMediaLogo = self::Logo->value . '/social_media';
    case SidebarLogo = self::Logo->value . '/sidebar';
    case StatisticsLogo = self::Logo->value . '/statistics';

    case FileLogo = self::Logo->value . '/files';

    case Dashboard = self::Logo->value . '/dashboard';
    case Info = self::Logo->value . '/info';

}
