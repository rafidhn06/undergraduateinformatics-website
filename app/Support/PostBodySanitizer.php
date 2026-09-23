<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;

final class PostBodySanitizer
{
    private HTMLPurifier $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'h2,h3,h4,p,ul,ol,li,table,thead,tbody,tr,th,td,a[href],b,strong,i,em,u,s,sub,sup,br,span,div,blockquote,hr,img[src|alt],pre,code');
        $config->set('HTML.TargetBlank', true);
        $config->set('Attr.AllowedRel', ['noopener', 'noreferrer']);
        $config->set('AutoFormat.AutoParagraph', false);
        $this->purifier = new HTMLPurifier($config);
    }

    public function sanitize(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $clean = trim($this->purifier->purify($html));

        if ($clean === '') {
            return null;
        }

        return $clean;
    }
}
