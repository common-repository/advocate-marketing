(function () {
    tinymce.PluginManager.add('sr_mce_button', function (editor, url) {
        editor.addButton('sr_mce_button', {
            image: url + '/swiftreview.png',
            title: 'Swift Reviews Shortcode Generator', //Tooltip
            type: 'menubutton',
            menu: [
                {
                    text: 'Swift Review Form',
                    onclick: function () {
                        editor.windowManager.open({
                            title: 'Swift Reviews',
                            body: [
                                {
                                    type: 'listbox',
                                    name: 'sr_rating_type',
                                    label: 'Rating type',
                                    minWidth: 100,
                                    values: [
                                        {text: '5 stars', value: '5stars'},
                                        {text: '10 stars', value: '10stars'},
                                        {text: 'Yes-No', value: 'yes-no'},
                                    ],
                                    value: '5stars' // Sets the default
                                },
                                {
                                    type: 'listbox',
                                    name: 'sr_category',
                                    label: 'Category',
                                    values: editor.settings.cptPostsList,
                                    tooltip: 'enter category slug.'
                                }
                            ],
                            onsubmit: function (e) {
                                var rating_type = (e.data.sr_rating_type === '') ? "" : ' rating_type="' + e.data.sr_rating_type + '"';
                                var category = (e.data.sr_category === '') ? "" : ' category="' + e.data.sr_category + '"';
                                editor.insertContent('[swift_review_form ' + rating_type + category + ']');
                            }
                        });
                    }
                },
                {
                    text: 'All Reviews Listing',
                    onclick: function () {
                        editor.windowManager.open({
                            title: 'Swift Reviews',
                            body: [
                                {
                                    type: 'listbox',
                                    name: 'sr_listing_category',
                                    label: 'Category',
                                    minWidth: 100,
                                    values: editor.settings.cptPostsList,
                                    id: 'sr_listing_category'
                                },
                                {
                                    type: 'listbox',
                                    name: 'sr_listing_style',
                                    label: 'Stars Style',
                                    minWidth: 100,
                                    values: [
                                        {text: '5 stars', value: '5stars'},
                                        {text: '10 stars', value: '10stars'}
                                    ],
                                    value: '5stars', // Sets the default
                                    id: 'sr_listing_style'
                                }
                            ],
                            onsubmit: function (e) {
                                var listing_cat = (e.data.sr_listing_category === '') ? "" : ' category="' + e.data.sr_listing_category + '"';
                                var stars_style = (e.data.sr_listing_style === '') ? "" : ' star_style="' + e.data.sr_listing_style + '"';
                                editor.insertContent('[swift_reviews_listing' + listing_cat + stars_style + ']');
                            }
                        });
                        // document.getElementById('sr_listing_category').innerHTML = (document.getElementById('srCats').innerHTML);
                    }
                },
                {
                    text: 'Positive Reviews Listing',
                    onclick: function () {
                        editor.windowManager.open({
                            title: 'Swift Reviews',
                            body: [
                                {
                                    type: 'listbox',
                                    name: 'sr_ps_listing_category',
                                    label: 'Category',
                                    values: editor.settings.cptPostsList,
                                    tooltip: 'enter category slug.'
                                },
                                {
                                    type: 'listbox',
                                    name: 'sr_ps_listing_style',
                                    label: 'Stars Style',
                                    minWidth: 100,
                                    values: [
                                        {text: '5 stars', value: '5stars'},
                                        {text: '10 stars', value: '10stars'}
                                    ],
                                    value: '5stars', // Sets the default
                                    id: 'sr_ps_listing_style'
                                }

                            ],
                            onsubmit: function (e) {
                                var listing_cat = (e.data.sr_ps_listing_category === '') ? "" : ' category="' + e.data.sr_ps_listing_category + '"';
                                var stars_style = (e.data.sr_ps_listing_style === '') ? "" : ' star_style="' + e.data.sr_ps_listing_style + '"';
                                editor.insertContent('[swift_positive_reviews' + listing_cat + stars_style + ']');
                            }
                        });
                    }
                },
                {
                    text: 'Reviews Listing Slider',
                    onclick: function () {
                        editor.windowManager.open({
                            title: 'Reviews Listing Slider',
                            body: [
//                                {
//                                    type: 'textbox',
//                                    name: 'sr_listing_slider_title',
//                                    label: 'Slider Title',
//                                    tooltip: 'Enter slider title',
//                                    value: ''
//                                },
                                {
                                    type: 'listbox',
                                    name: 'sr_listing_slider_category',
                                    label: 'Category',
                                    values: editor.settings.cptPostsList,
                                    tooltip: 'Select category'
                                },
                                {
                                    type: 'listbox',
                                    name: 'sr_listing_slider_style',
                                    label: 'Style',
                                    minWidth: 200,
                                    values: [
                                        {text: 'Select Style', value: '0'},
                                        {text: 'Small - Sidebar Stars', value: 'sr_style_1'},
                                        {text: 'Large + Dark Theme Carousel', value: 'sr_style_2'},
                                        {text: 'Standard Carousel', value: 'sr_style_3'},
                                        {text: 'Big Quote Text Style', value: 'sr_style_4'}
                                    ],
                                    value: '0', // Sets the default
                                    id: 'sr_listing_slider_style'
                                },
                                {
                                    type: 'textbox',
                                    name: 'sr_listing_slider_length',
                                    label: 'Number of reviews to show',
                                    tooltip: 'Number of reviews to show. Leave blank to show all reviews',
                                    value: ''
                                },

                            ],
                            onsubmit: function (e) {
                                var slider_title = ''; //(e.data.sr_listing_slider_title === '') ? "" : ' title="' + e.data.sr_listing_slider_title + '"';
                                var listing_cat = (e.data.sr_listing_slider_category === '0') ? "" : ' category="' + e.data.sr_listing_slider_category + '"';
                                var stars_style = (e.data.sr_listing_slider_style === '0') ? "" : ' style="' + e.data.sr_listing_slider_style + '"';
                                var slider_length = (e.data.sr_listing_slider_length === '') ? "" : ' no_of_review="' + e.data.sr_listing_slider_length + '"';
                                editor.insertContent('[swift_review_slider' + slider_title + listing_cat + stars_style + slider_length + ']');
                            }
                        });
                    }
                },
            ]
        });
    });
})();