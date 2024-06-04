export const useFormUploadParams = (key, upload) => {
    return {
        [`${key}_uri`]: upload?.url ?? null,
        [`${key}_upload`]: false,
        [`${key}_path`]: upload?.path ?? '',
    };
};