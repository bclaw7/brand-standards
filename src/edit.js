import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, InspectorControls, InnerBlocks } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';
import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	const { leftColumnWidth, heading } = attributes;
	const blockProps = useBlockProps();

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Column Settings', 'brand-standards')}>
					<RangeControl
						label={__('Left Column Width (%)', 'brand-standards')}
						value={leftColumnWidth}
						onChange={(newWidth) => setAttributes({ leftColumnWidth: newWidth })}
						min={10}
						max={90}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>
				<div className="wp-block-columns brand-standards-columns">
					<div className="wp-block-column" style={{ flexBasis: `${leftColumnWidth}%` }}>
						<RichText
							tagName="h2"
							value={heading}
							onChange={(newHeading) => setAttributes({ heading: newHeading })}
							placeholder={__('Section Heading', 'brand-standards')}
						/>
					</div>
					<div className="wp-block-column" style={{ flexBasis: `${100 - leftColumnWidth}%` }}>
						<InnerBlocks
							allowedBlocks={['core/paragraph', 'core/list']}
							template={[
								['core/paragraph', { placeholder: __('Add your content here.', 'brand-standards') }],
							]}
						/>
					</div>
				</div>
			</div>
		</>
	);
}