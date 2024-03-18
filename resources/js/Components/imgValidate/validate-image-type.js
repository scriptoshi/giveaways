import assert from "assert";
import isSvg from "is-svg";
import { imageType } from "./image-type";
/**
 * Detect the image type of Buffer or Uint8Array
 * This check is based on https://github.com/sindresorhus/image-type
 */
export async function validateBufferMIMEType(
    buffer,
    allowMimeTypes
) {

    assert.ok(
        Array.isArray(allowMimeTypes) && allowMimeTypes.every((mimeType) => mimeType.includes("/")),
        `Should be set an array of mimeType. e.g.) ['image/jpeg']`
    );
    const allowSVG = allowMimeTypes.includes("image/svg+xml");
    console.log(buffer, isSvg(buffer))
    if (allowSVG && isSvg(buffer)) {
        return {
            ok: true,
            error: undefined
        };
    }
    const imageTypeResult = await imageType(buffer);
    if (!imageTypeResult) {
        return {
            ok: false,
            error: new Error(
                `This buffer is not supported image. allowMimeTypes: ${JSON.stringify(allowMimeTypes)}`
            )
        };
    }
    const isAllowed = allowMimeTypes.includes(imageTypeResult.mime);
    if (!isAllowed) {
        return {
            ok: false,
            error: new Error(
                `This buffer is disallowed image MimeType: ${imageTypeResult.mime}, allowMimeTypes: ${JSON.stringify(
                    allowMimeTypes
                )}`
            )
        };
    }
    return {
        ok: true,
        error: undefined
    };
}
