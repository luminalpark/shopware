/**
 * @sw-package framework
 * @deprecated tag:v6.7.0 - Will be replaced with Pinia store
 */
// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    namespaced: true,

    state() {
        const today = new Date().toISOString();

        return {
            isFlowTriggered: true,
            orderDocuments: {
                invoice: {
                    isChanged: false,
                    value: {
                        documentDate: today,
                        documentComment: null,
                        forceDocumentCreation: false,
                    },
                },
                storno: {
                    isChanged: false,
                    value: {
                        documentDate: today,
                        documentComment: null,
                        forceDocumentCreation: false,
                    },
                },
                delivery_note: {
                    isChanged: false,
                    value: {
                        custom: {
                            deliveryDate: today,
                            deliveryNoteDate: today,
                        },
                        documentDate: today,
                        documentComment: null,
                        forceDocumentCreation: false,
                    },
                },
                credit_note: {
                    isChanged: false,
                    value: {
                        documentDate: today,
                        documentComment: null,
                        forceDocumentCreation: false,
                    },
                },
                download: {
                    isChanged: false,
                    value: [],
                },
            },
        };
    },

    mutations: {
        setIsFlowTriggered(state, isFlowTriggered) {
            state.isFlowTriggered = isFlowTriggered;
        },
        setOrderDocumentsIsChanged(state, { type, isChanged }) {
            state.orderDocuments[type].isChanged = isChanged;
        },
        setOrderDocumentsValue(state, { type, value }) {
            state.orderDocuments[type].value = value;
        },
    },

    getters: {
        documentTypeConfigs(state) {
            const documentTypeConfigs = [];

            Object.entries(state.orderDocuments).forEach(
                ([
                    key,
                    value,
                ]) => {
                    if (key === 'download') {
                        return;
                    }
                    if (value.isChanged === true) {
                        documentTypeConfigs.push({
                            fileType: 'pdf',
                            type: key,
                            config: value.value,
                        });
                    }
                },
            );

            return documentTypeConfigs;
        },
    },
};
