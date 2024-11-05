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
                source: 'html',
                selector: 'h2',
                default: ''
            }
        },
        supports: {
            html: true
        },
        save: ({ attributes }) => {
            const { leftColumnWidth, heading } = attributes;
            const blockProps = useBlockProps.save({
                className: 'wp-block-brand-standards-brand-guide-section'
            });
            
            return (
                <div {...blockProps}>
                    <div className="wp-block-columns">
                        <div className="wp-block-column" style={{ flexBasis: `${leftColumnWidth}%` }}>
                            {heading && <h2>{heading}</h2>}
                        </div>
                        <div className="wp-block-column" style={{ flexBasis: `${100 - leftColumnWidth}%` }}>
                            <InnerBlocks.Content />
                        </div>
                    </div>
                </div>
            );
        }
    }
];

export default deprecated;