<?php

namespace Migunov\Services;

use DOMDocument;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Helper
{
    public const USER_AGENT = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36';

    /**
     * Extract file extension from path. For example: "storage/stores/logo.svg" -> ".svg"
     */
    public static function fileExtension(string $path): string
    {
        return preg_replace('#.+(\..+)$#', '$1', $path);
    }

    public static function fileExtensionFromMime(string $contentType): string
    {
        if (strpos($contentType, 'image/') === false) {
            return '';
        }

        $ext = explode('/', $contentType, 2)[1];
        $ext = str_ireplace('+xml', '', $ext); // image/svg+xml

        return '.' . $ext;
    }

    public static function getMetaTags(string $url): array
    {
        libxml_use_internal_errors(true);

        $result = [];
        $html = self::httpClient()->get($url)->body();
        $dom = new DOMDocument();

        try {
            if (!$dom->loadHTML($html)) {
                return $result;
            }
        } catch(Exception $e) {
            return $result;
        }

        $metas = $dom->getElementsByTagName('meta');

        /** @var DOMElement */
        foreach ($metas as $meta) {
            $key = $meta->getAttribute('name') ?: $meta->getAttribute('property');

            if ($key) {
                $result[$key] = $meta->getAttribute('content');
            }
        }

        return $result;
    }

    public static function host(string $url): string
    {
        return preg_replace('#^https?\://([^/]+).*$#', '$1', $url);
    }

    public static function htmlCut($text, $max_length): string
    {
        $tags = [];
        $result = '';

        $is_open = false;
        $grab_open = false;
        $is_close = false;
        $in_double_quotes = false;
        $in_single_quotes = false;
        $tag = '';

        $i = 0;
        $stripped = 0;

        $stripped_text = strip_tags($text);

        while (
            $i < mb_strlen($text)
            && $stripped < mb_strlen($stripped_text)
            && $stripped < $max_length
        ) {
            $symbol = mb_substr($text, $i, 1);
            $result .= $symbol;

            switch ($symbol) {
                case '<':
                    $is_open = true;
                    $grab_open = true;
                    break;

                case '"':
                    $in_double_quotes = !$in_double_quotes;
                    break;

                case "'":
                    $in_single_quotes = !$in_single_quotes;
                    break;

                case '/':
                    if ($is_open && !$in_double_quotes && !$in_single_quotes) {
                        $is_close = true;
                        $is_open = false;
                        $grab_open = false;
                    }
                    break;

                case ' ':
                    if ($is_open) {
                        $grab_open = false;
                    } else {
                        $stripped++;
                    }
                    break;

                case '>':
                    if ($is_open) {
                        $is_open = false;
                        $grab_open = false;
                        array_push($tags, $tag);
                        $tag = '';
                    } elseif ($is_close) {
                        $is_close = false;
                        array_pop($tags);
                        $tag = '';
                    }

                    break;

                default:
                    if ($grab_open || $is_close) {
                        $tag .= $symbol;
                    }

                    if (!$is_open && !$is_close) {
                        $stripped++;
                    }
            }

            $i++;
        }

        while ($tags) {
            $result .= '</' . array_pop($tags) . '>';
        }

        return $result;
    }

    public static function httpClient(): PendingRequest
    {
        return Http::withUserAgent(self::USER_AGENT);
    }

    /**
     * Clean string values in data.
     */
    public static function sanitizeData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }

        return $data;
    }

    public static function socialName(string $url): string
    {
        if (!Str::isUrl($url)) {
            return '';
        }

        $socials = [
            'facebook' => 'facebook.com',
            'github' => 'github.com',
            'instagram' => 'instagram.com',
            'linkedin' => 'linkedin.com',
            'mastodon' => 'mastodon',
            'x-twitter' => 'twitter.com',
            'youtube' => 'youtube.com',
        ];

        foreach ($socials as $key => $host) {
            if (preg_match('#' . $host . '#i', $url)) {
                return $key;
            }
        }

        $host = preg_replace('#https?://([^\/]+?)(/.+)?$#i', '$1', $url);
        return str_ireplace('www.', '', $host);
    }

    /**
     * Разбить строку на массив с данными.
     * Все значения будут зачищены от лишних символов вроде пробелов.
     */
    public static function stringToArray(
        string $value,
        string $separator = ','
    ): array {
        return $separator
                ? self::sanitizeData(explode($separator, $value))
                : [$value];
    }

    public static function timeFormat(int $seconds): string
    {
        if ($seconds < 10) {
            return '';
        }

        if ($seconds <= 60) {
            return '1 mimute';
        }

        if ($seconds < 3600) {
            $minutes = round($seconds / 60);
            return $minutes . ($minutes < 2 ? 'minute' : ' minutes');
        }

        if ($seconds < 86400) {
            $hours = round($seconds / 3600);
            $minutes = round(($seconds % 3600) / 60);
            return "{$hours} hr. {$minutes} min.";
        }

        $days = round($seconds / 86400);
        $hours = round(($seconds % 86400) / 3600);
        $minutes = round((($seconds % 86400) % 3600) / 60);

        return "{$days} d. {$hours} hr. {$minutes} min.";
    }
}
