/**
 * @sw-package framework
 */

Shopware.Filter.register('asset', (value: string) => {
    if (!value) {
        return '';
    }

    // Asset path already stars with an slash. Double slashes does not work on external storage like s3
    if (value[0] === '/') {
        value = value.substr(1);
    }

    const assetsPath = Shopware.Context.api.assetsPath || '';

    return `${assetsPath}${value}`;
});

/**
 * @private
 */
export {};
