require([
    'jquery'
], function ($) {


    (function () {

        var object = {};
        var form = $('#review-form');

        var isApplicableCache = [];

        var collection = jsonConfig;

        object.init = function () {
            $(document).on('change', form, function (e) {
                var discount = calculateDiscount();
            });
            triggerShare();
            fileUploader();

        };


        function triggerShare() {
            var shareLink = '.social-links.share a';
            $(document).on('click', shareLink, function () {
                $(this).siblings(':radio').prop('checked', true).trigger( "change" );

            });
        }

        function calculateDiscount() {
            var discount = 0;
            isApplicableCache = [];
            /** @var DiscountRule $discountRule */
            for (var i = 0; i < collection.length; i++) {
                if (isApplicable(collection[i])) {
                    discount += +collection[i]['discount'];
                }
            }
            changeDiscountBar(discount);
            return discount;

        }

        function changeDiscountBar(discount) {
            var stepSize = 2.5;
            var discountBar = '.fill-level-bar';
            var barMaxValue = $(discountBar).data('maxDiscount');
            var stepsCount = Math.ceil(barMaxValue/stepSize);
            var stepsActiveCount = Math.ceil(discount/stepSize);

            var fillLevel = (100/stepsCount*stepsActiveCount)+'%';
            $(discountBar).find('.discount-level').css("width", fillLevel);
        }

        function fileUploader() {
            var fileField = '.review-field-image';
            var fileInput = fileField+' input:file';
            var removeFileBtn = '.file-name-remove';
            $(document).on('change', fileInput, function () {
                var file = $(this)[0].files[0];
                if (file) {
                    $(this).closest(fileField).find('.file-name').html(file.name);
                    $(this).closest(fileField).find(removeFileBtn).show();
                }
            });
            $(document).on('click', removeFileBtn, function () {
                $(this).closest(fileField).find('input[type="file"]').val('');
                $(this).siblings('.file-name').html('');
                $(this).hide().trigger('change');
                return false;
            });
        }

        /**
         * @return boolean
         * @param discountRule
         */
        var isApplicable = function (discountRule) {
            var id = discountRule.entity_id;

            if (isApplicableCache[id] === undefined) {
                var isApplicable = true;
                if (discountRule.validator) {
                    var field = discountRule.input_field;
                    var fieldItem = $('[name*=' + field + ']', form);
                    if (fieldItem.attr('type') !== 'radio') {
                        var fieldData = fieldItem.not(':radio').val();
                    } else {
                        //TODO_EO_FE handle inputs for rating
                        var fieldData = $('[name*=' + field + ']:checked', form).val();
                    }

                    isApplicable = !!fieldData;

                }
                var dependenciesApplicable = calculateDependencies(discountRule);
                isApplicableCache[id] = isApplicable && dependenciesApplicable;
            }

            return isApplicableCache[id];
        };

        var calculateDependencies = function (discountRule) {
            var dependencies = getDependenciesForRule(discountRule);

            var dependenciesApplicableCount = 0;
            var dependencyCondition = discountRule.dependency_condition ? discountRule.dependency_condition : dependencies.length;

            for (var i = 0; i < dependencies.length; i++) {
                var dependency = dependencies[i];
                if (isApplicable(dependency)) {
                    dependenciesApplicableCount++;
                }
            }
            return dependenciesApplicableCount >= dependencyCondition;
        };

        var getDependenciesForRule = function (discountRule) {
            var result = [];
            if (discountRule.depends) {
                var dependenciesIds = discountRule.depends.split(',');
                for (var i = 0; i < collection.length; i++) {
                    var entityId = collection[i].entity_id;
                    if ($.inArray(entityId, dependenciesIds) !== -1) {
                        result.push(collection[i]);
                    }
                }
            }
            return result;
        };

        object.init();

    })();

});

