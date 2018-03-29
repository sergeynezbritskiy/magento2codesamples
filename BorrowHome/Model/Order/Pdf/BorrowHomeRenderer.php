<?php

namespace Magecom\BorrowHome\Model\Order\Pdf;

use Magecom\BorrowHome\Model\Order\DataProvider\BorrowHomeOrderItemsDataProvider;
use Magecom\OrderPreparation\Model\Order\Pdf\Element;
use sergeynezbritskiy\ZendPdfTable\AbstractElement;

/**
 * Class BorrowHomeRenderer
 *
 * @package Magecom\OrderPreparation\Model\Order\Pdf
 */
class BorrowHomeRenderer extends \Magecom\OrderPreparation\Model\Order\Pdf\AbstractRenderer
{

    /**
     * @return string
     * @see \Zend_Pdf_Page return should be one of page constants
     */
    public function getPageSize()
    {
        return \Zend_Pdf_Page::SIZE_A4;
    }

    /**
     * Elements list to print within page
     *
     * @return array
     */
    protected function getData()
    {
        $lineWidth = 1.2;
        $table = [
            'class' => Element\Table::class,
            'data' => [
                'body' => [
                    'columns' => [
                        'column_1' => [
                            'data' => '{{ items[0] }}',
                            'options' => [
                                'font_style' => \Zend_Pdf_Font::FONT_HELVETICA_BOLD,
                                'width' => 90,
                                'border_left' => [
                                    'line_color' => [0, 0, 0],
                                    'line_width' => $lineWidth,
                                ],
                                'content_type' => AbstractElement::CONTENT_TYPE_TEXT,
                                'text_align' => AbstractElement::LEFT
                            ]
                        ],
                        'column_2' => [
                            'data' => '{{ items[1] }}',
                            'options' => [
                                'width' => 120,
                                'background_color' => [230, 230, 230],
                            ],
                        ],
                        'column_3' => [
                            'data' => '{{ items[2] }}',
                            'options' => [
                                'width' => 120,
                                'background_color' => [210, 210, 210],
                            ],
                        ],
                        'column_4' => [
                            'data' => '{{ items[3] }}',
                            'options' => [
                                'width' => 120,
                                'background_color' => [230, 230, 230],
                            ],
                        ],
                        'column_5' => [
                            'data' => '{{ items[4] }}',
                            'options' => [
                                'width' => 120,
                                'background_color' => [210, 210, 210],
                                'border_right' => [
                                    'line_color' => [0, 0, 0],
                                    'line_width' => $lineWidth,
                                ]
                            ],
                        ],
                    ],
                    'data_provider' => $this->objectManagerInterface->create(BorrowHomeOrderItemsDataProvider::class, ['order' => $this->order]),
                ],
            ],
            'options' => [
                'font_size' => 9,
                'font_style' => \Zend_Pdf_Font::FONT_HELVETICA,
                'fill_color' => [0, 0, 0],
                'borders' => [
                    AbstractElement::LEFT => [
                        'line_color' => [0, 0, 0],
                        'line_width' => $lineWidth / 2,
                    ],
                    AbstractElement::RIGHT => [
                        'line_color' => [0, 0, 0],
                        'line_width' => $lineWidth / 2,
                    ],
                    AbstractElement::BOTTOM => [
                        'line_color' => [0, 0, 0],
                        'line_width' => $lineWidth,
                    ],
                    AbstractElement::TOP => [
                        'line_color' => [0, 0, 0],
                        'line_width' => $lineWidth,
                    ],
                ],
                'paddings' => [
                    AbstractElement::BOTTOM => 2,
                    AbstractElement::TOP => 3,
                    AbstractElement::LEFT => 1,
                    AbstractElement::RIGHT => 1,
                ],
                'margins' => [
                    AbstractElement::LEFT => 15,
                    AbstractElement::RIGHT => 15,
                ],
                'first_row' => [
                    'text_align' => AbstractElement::CENTER,
                ],
                'row_2' => [
                    'text_align' => AbstractElement::CENTER,
                    'content_type' => AbstractElement::CONTENT_TYPE_IMAGE,
                    'content_width' => 110,
                    'content_height' => 80,
                    'padding_top' => 10,
                    'padding_bottom' => 10,
                ],
                'row_9' => [
                    'text_align' => AbstractElement::CENTER,
                    'content_type' => AbstractElement::CONTENT_TYPE_IMAGE,
                    'content_width' => 70,
                    'content_height' => 70,
                ],
            ],
            'position' => [
                'x' => 0,
                'y' => 110,
            ],
        ];
        return [
            'company_logo_left' => [
                'class' => Element\Image::class,
                'position' => [
                    'x' => 38.5,
                    'y' => 823,
                    'width' => 53,
                    'height' => 53,
                ],
                'data' => '{{ logo }}',
                'options' => []
            ],
            'company_title_left' => [
                'class' => Element\Text::class,
                'position' => [
                    'x' => 22,
                    'y' => 780,
                    'width' => 20,
                    'height' => 10,
                ],
                'data' => __('Extra Optical'),
                'options' => [
                    'font_size' => 15,
                    'font_style' => \Zend_Pdf_Font::FONT_HELVETICA,
                    'fill_color' => [0, 0, 0]
                ],
            ],
            'delivery_note' => [
                'class' => Element\Text::class,
                'position' => [
                    'x' => 28,
                    'y' => 755,
                    'width' => 20,
                    'height' => 10,
                ],
                'data' => __('Delivery Note'),
                'options' => [
                    'font_size' => 12,
                    'font_style' => \Zend_Pdf_Font::FONT_HELVETICA,
                    'fill_color' => [0, 0, 0]
                ],
            ],
            'line' => [
                'class' => Element\Rectangle::class,
                'position' => [
                    'x' => 125,
                    'y' => 827,
                    'width' => 0.3,
                    'height' => 54,
                ],
                'data' => '',
                'options' => [
                    'font_size' => 12,
                    'font_style' => \Zend_Pdf_Font::FONT_HELVETICA,
                    'line_color' => [0, 0, 0]
                ],
            ],
            'make_me_thinner' => [
                'class' => Element\Rectangle::class,
                'position' => [
                    'x' => 125.4,
                    'y' => 828,
                    'width' => 0.3,
                    'height' => 56,
                ],
                'data' => '',
                'options' => [
                    'font_size' => 12,
                    'font_style' => \Zend_Pdf_Font::FONT_HELVETICA,
                    'line_color' => [255, 255, 255]
                ],
            ],
            'order_id' => [
                'class' => Element\Text::class,
                'position' => [
                    'x' => 148,
                    'y' => 814,
                ],
                'data' => 'Order: {{ order.incrementId }}',
                'options' => [
                    'font_size' => 14,
                    'font_style' => \Zend_Pdf_Font::FONT_HELVETICA,
                    'fill_color' => [0, 0, 0]
                ],
            ],
            'order_date' => [
                'class' => Element\Text::class,
                'position' => [
                    'x' => 148,
                    'y' => 796,
                ],
                'data' => 'Date: {{ order.createdAt|date("d.m.Y") }}',
                'options' => [
                    'font_size' => 14,
                    'font_style' => \Zend_Pdf_Font::FONT_HELVETICA,
                    'fill_color' => [0, 0, 0]
                ],
            ],
            'barcode_left' => [
                'class' => Element\Barcode::class,
                'position' => [
                    'x' => 297,
                    'y' => 834,
                    'width' => 80,
                    'height' => 30,
                ],
                'data' => '{{ order.incrementId }}',
                'options' => [
                    'font_size' => 22,
                    'font_style' => \Zend_Pdf_Font::FONT_HELVETICA_BOLD,
                    'fill_color' => [0, 0, 0]
                ],
            ],
            'order_id_left' => [
                'class' => Element\Text::class,
                'position' => [
                    'x' => 313,
                    'y' => 794,
                ],
                'data' => '{{ order.incrementId }}',
                'options' => [
                    'font_size' => 10,
                    'font_style' => \Zend_Pdf_Font::FONT_HELVETICA,
                    'fill_color' => [0, 0, 0]
                ],
            ],
            'order_date_left' => [
                'class' => Element\Text::class,
                'position' => [
                    'x' => 320,
                    'y' => 780,
                ],
                'data' => 'Date {{ order.createdAt|date("n/j") }}',
                'options' => [
                    'font_size' => 8,
                    'font_style' => \Zend_Pdf_Font::FONT_HELVETICA,
                    'fill_color' => [0, 0, 0]
                ],
            ],
            'barcode_right' => [
                'class' => Element\Barcode::class,
                'position' => [
                    'x' => 497,
                    'y' => 831,
                    'width' => 80,
                    'height' => 30,
                ],
                'data' => '{{ order.incrementId }}',
                'options' => [
                    'font_size' => 22,
                    'font_style' => \Zend_Pdf_Font::FONT_HELVETICA_BOLD,
                    'fill_color' => [0, 0, 0]
                ],
            ],
            'order_id_right' => [
                'class' => Element\Text::class,
                'position' => [
                    'x' => 513,
                    'y' => 791,
                ],
                'data' => '{{ order.incrementId }}',
                'options' => [
                    'font_size' => 10,
                    'font_style' => \Zend_Pdf_Font::FONT_HELVETICA,
                    'fill_color' => [0, 0, 0]
                ],
            ],
            'order_date_right' => [
                'class' => Element\Text::class,
                'position' => [
                    'x' => 520,
                    'y' => 777,
                ],
                'data' => 'Date {{ order.createdAt|date("n/j") }}',
                'options' => [
                    'font_size' => 8,
                    'font_style' => \Zend_Pdf_Font::FONT_HELVETICA,
                    'fill_color' => [0, 0, 0]
                ],
            ],
            'table' => $table,
            'disclaimer' => [
                'class' => Element\Image::class,
                'position' => [
                    'x' => 17,
                    'y' => 470,
                    'width' => 562,
                    'height' => 562,
                ],
                'data' => '{{ disclaimer_borrow_home }}',
                'options' => []
            ],
        ];
    }
}