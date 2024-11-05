import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import Edit from './edit';
import Save from './save';
import './style.scss';

registerBlockType('brand-standards/logo-tabs', {
    title: __('Logo Tabs', 'brand-standards'),
    description: __('Display logo variations with tabbed interface', 'brand-standards'),
    category: 'design',
    icon: 'image-flip-horizontal',
    parent: ['brand-standards/brand-guide-section'],
    supports: {
        html: false,
        align: ['wide', 'full']
    },
    attributes: {
        activeTab: {
            type: 'string',
            default: 'primary'
        },
        tabs: {
            type: 'array',
            default: [
                {
                    id: 'primary',
                    label: 'Primary Logo',
                    content: ''
                },
                {
                    id: 'reversed',
                    label: 'Reversed',
                    content: ''
                },
                {
                    id: 'icon',
                    label: 'Icon',
                    content: ''
                },
                {
                    id: 'wordmark',
                    label: 'Wordmark',
                    content: ''
                },
                {
                    id: 'variations',
                    label: 'Color Variations',
                    content: ''
                }
            ]
        }
    },
    edit: Edit,
    save: Save,
});