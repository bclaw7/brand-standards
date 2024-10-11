import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save({ attributes }) {
	const { leftColumnWidth, heading } = attributes;
	const blockProps = useBlockProps.save();

	return (
		<div {...blockProps}>
			<div className="wp-block-columns brand-standards-columns">
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