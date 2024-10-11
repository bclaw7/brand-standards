import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

const deprecated = [
    {
        attributes: {
            leftColumnWidth: {
                type: 'number',
                default: 33.33
            },
            heading: {
                type: 'string',
                default: ''
            },
        },
        save: ({ attributes }) => {
            const { leftColumnWidth, heading } = attributes;
            return (
                <div {...useBlockProps.save()}>
                    <div className="wp-block-columns">
                        <div className="wp-block-column" style={{ flexBasis: `${leftColumnWidth}%` }}>
                            <h2>{heading}</h2>
                        </div>
                        <div className="wp-block-column" style={{ flexBasis: `${100 - leftColumnWidth}%` }}>
                            <InnerBlocks.Content />
                        </div>
                    </div>
                </div>
            );
        },
    },
    {
        attributes: {
            leftColumnWidth: {
                type: 'number',
                default: 33.33
            },
            heading: {
                type: 'string',
                default: ''
            },
        },
        save: ({ attributes }) => {
            const { leftColumnWidth, heading } = attributes;
            return (
                <div {...useBlockProps.save()}>
                    <div className="wp-block-columns">
                        <div className="wp-block-column" style={{ flexBasis: `${leftColumnWidth}%` }}>
                            <h2>{heading}</h2>
                        </div>
                        <div className="wp-block-column" style={{ flexBasis: `${100 - leftColumnWidth}%` }}>
                        </div>
                    </div>
                </div>
            );
        },
    },
    {
        attributes: {
            leftColumnWidth: {
                type: 'number',
                default: 33.33
            },
            heading: {
                type: 'string',
                default: ''
            },
        },
        save: () => null,
    },
];

export default deprecated;