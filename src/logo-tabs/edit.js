import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import LogoTabsSection from './components/logoTabsSection';

export default function Edit({ attributes, setAttributes }) {
    return (
        <div {...useBlockProps()}>
            <LogoTabsSection
                attributes={attributes}
                setAttributes={setAttributes}
                isEditor={true}
            />
        </div>
    );
}
