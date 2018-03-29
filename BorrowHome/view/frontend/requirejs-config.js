var config = {
    paths: {
        'borrow': 'Magecom_BorrowHome/js/borrow',
        'vue' : 'Magecom_Extraoptical/js/vue',
        'minicartborrow' : 'Magecom_BorrowHome/js/minicart-borrow-home'
    },
    shim : {
        'minicartborrow': {
            deps: ['Magento_Customer/js/customer-data']
        }
    }
};