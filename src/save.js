import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { leftColumnWidth, heading } = attributes;
    const rightColumnWidth = (100 - leftColumnWidth).toFixed(2); // Ensure consistent decimal places
    
    const blockProps = useBlockProps.save({
        className: 'wp-block-brand-standards-brand-guide-section'
    });

    return (
        <div {...blockProps}>
            <div className="wp-block-columns">
                <div className="wp-block-column" style={{ flexBasis: `${leftColumnWidth}%` }}>
                    {heading && <h2>{heading}</h2>}
                </div>
                <div className="wp-block-column" style={{ flexBasis: `${rightColumnWidth}%` }}>
                    <InnerBlocks.Content />
                </div>
            </div>
        </div>
    );
}