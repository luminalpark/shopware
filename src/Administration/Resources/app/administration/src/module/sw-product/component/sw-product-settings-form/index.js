/*
 * @sw-package inventory
 */

import template from './sw-product-settings-form.html.twig';

const { mapPropertyErrors, mapState } = Shopware.Component.getComponentHelper();

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    compatConfig: Shopware.compatConfig,

    props: {
        allowEdit: {
            type: Boolean,
            required: false,
            // eslint-disable-next-line vue/no-boolean-default
            default: true,
        },
    },

    computed: {
        ...mapState('swProductDetail', [
            'product',
            'parentProduct',
        ]),

        ...mapPropertyErrors('product', [
            'releaseDate',
            'stock',
            'minPurchase',
            'maxPurchase',
            'ean',
            'manufacturerNumber',
            'shippingFree',
            'markAsTopseller',
        ]),
    },
};
