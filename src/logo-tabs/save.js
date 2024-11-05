import { useBlockProps } from '@wordpress/block-editor';
import LogoTabsSection from './components/logoTabsSection';

export default function Save({ attributes }) {
    return (
        <div {...useBlockProps.save()}>
            <LogoTabsSection
                attributes={attributes}
                isEditor={false}
            />
        </div>
    );
}