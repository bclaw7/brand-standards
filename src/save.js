import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { leftColumnWidth, heading } = attributes;
    
    // Get block props and ensure they're properly applied
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