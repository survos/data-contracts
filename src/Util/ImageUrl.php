<?php

declare(strict_types=1);

namespace Survos\DataContracts\Util;

/**
 * Shape checks for source image URLs, shared by the harvest-time ingest gates and the
 * render-time thumbnail path so both agree on what "this is an image" means.
 *
 * Aggregator metadata regularly puts a non-image asset in a field we treat as the image:
 * EDAN/Smithsonian occasionally supplies a MODS finding-aid viewer config XML, and
 * Europeana/museum-digital routinely supply a PDF (the digitised document *is* the asset).
 * Both 403 or fail to decode when handed to imgproxy, which surfaces to the user as a
 * broken thumbnail rather than as an actionable error (survos-sites/musdig#40).
 *
 * Deliberately extension-based and offline: image delivery endpoints are commonly
 * extension-less (`ids.si.edu/ids/deliveryService?id=...`, IIIF bases), so requiring a known
 * image extension would reject far more good URLs than it caught bad ones. We reject on an
 * explicit non-image extension instead, and leave content-type confirmation to callers that
 * can afford a network round trip.
 */
final class ImageUrl
{
    /**
     * Extensions that are definitely not a rasterisable image asset.
     *
     * PDF is separated out because it is a different failure: the XML/HTML cases are a
     * *wrong field* picked at harvest time, whereas a PDF is genuinely the record's asset
     * and wants a document placeholder rather than rejection. {@see classify()}.
     */
    public const NON_IMAGE_EXTENSIONS = ['xml', 'html', 'htm', 'json', 'txt', 'csv', 'zip'];

    public const DOCUMENT_EXTENSIONS = ['pdf', 'doc', 'docx', 'epub'];

    /** Extensions we positively recognise as raster images. */
    public const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'tif', 'tiff', 'webp', 'avif', 'bmp', 'jp2'];

    /**
     * False only when the URL carries an extension we know imgproxy cannot render.
     * Extension-less URLs (delivery services, IIIF bases) pass — see the class docblock.
     */
    public static function looksLikeImage(string $url): bool
    {
        return self::classify($url) !== ImageUrlVerdict::NotAnImage
            && self::classify($url) !== ImageUrlVerdict::Document;
    }

    /**
     * Why a URL is (or isn't) usable as an image, so callers can tell "harvest picked the
     * wrong field" apart from "this record's asset is a PDF" and react differently.
     */
    public static function classify(string $url): ImageUrlVerdict
    {
        $url = trim($url);
        if ($url === '') {
            return ImageUrlVerdict::Empty;
        }

        $scheme = strtolower((string) (parse_url($url, PHP_URL_SCHEME) ?? ''));
        if ($scheme !== '' && !in_array($scheme, ['http', 'https'], true)) {
            return ImageUrlVerdict::NotAnImage;
        }

        $ext = self::extension($url);
        if ($ext === null) {
            // Extension-less: a delivery endpoint or IIIF base. Assume good.
            return ImageUrlVerdict::Unverifiable;
        }
        if (in_array($ext, self::DOCUMENT_EXTENSIONS, true)) {
            return ImageUrlVerdict::Document;
        }
        if (in_array($ext, self::NON_IMAGE_EXTENSIONS, true)) {
            return ImageUrlVerdict::NotAnImage;
        }
        if (in_array($ext, self::IMAGE_EXTENSIONS, true)) {
            return ImageUrlVerdict::Image;
        }

        return ImageUrlVerdict::Unverifiable;
    }

    /**
     * Lowercased extension of the URL's *path*, ignoring query and fragment, or null when
     * there isn't one. Query strings are excluded on purpose: `deliveryService?id=X_001.tif`
     * is an image endpoint whose query happens to end in an extension.
     */
    public static function extension(string $url): ?string
    {
        $path = (string) (parse_url($url, PHP_URL_PATH) ?? '');
        if ($path === '') {
            return null;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return $ext === '' ? null : $ext;
    }

    /** True when the Content-Type of a fetched response is a renderable image. */
    public static function isImageContentType(?string $contentType): bool
    {
        if ($contentType === null || $contentType === '') {
            return false;
        }

        return str_starts_with(strtolower(trim($contentType)), 'image/');
    }
}
