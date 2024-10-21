import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, Button } from '@wordpress/components';

registerBlockType('brand-standards/campaign-report', {
    title: __('Campaign Report', 'brand-standards'),
    icon: 'chart-bar',
    category: 'design',
    attributes: {
        mediaId: {
            type: 'number',
        },
        mediaUrl: {
            type: 'string',
        },
    },
    edit: ({ attributes, setAttributes }) => {
        const { mediaId, mediaUrl } = attributes;

        const onSelectMedia = (media) => {
            setAttributes({
                mediaId: media.id,
                mediaUrl: media.url,
            });
        };

        return (
            <>
                <InspectorControls>
                    <PanelBody title={__('Report Settings', 'brand-standards')}>
                        <MediaUploadCheck>
                            <MediaUpload
                                onSelect={onSelectMedia}
                                allowedTypes={['application/pdf']}
                                value={mediaId}
                                render={({ open }) => (
                                    <Button
                                        onClick={open}
                                        isPrimary={true}
                                    >
                                        {mediaId ? __('Replace PDF', 'brand-standards') : __('Upload PDF', 'brand-standards')}
                                    </Button>
                                )}
                            />
                        </MediaUploadCheck>
                    </PanelBody>
                </InspectorControls>
                <div {...useBlockProps()}>
                    {mediaUrl ? (
                        <div>
                            <h3>{__('Campaign Report', 'brand-standards')}</h3>
                            <a href={mediaUrl} target="_blank" rel="noopener noreferrer">{__('View Report', 'brand-standards')}</a>
                        </div>
                    ) : (
                        <p>{__('Upload a PDF report in the block settings.', 'brand-standards')}</p>
                    )}
                </div>
            </>
        );
    },
    save: ({ attributes }) => {
        const { mediaUrl } = attributes;
        return (
            <div {...useBlockProps.save()}>
                {mediaUrl && (
                    <div>
                        <h3>{__('Campaign Report', 'brand-standards')}</h3>
                        <a href={mediaUrl} target="_blank" rel="noopener noreferrer">{__('View Report', 'brand-standards')}</a>
                    </div>
                )}
            </div>
        );
    },
});