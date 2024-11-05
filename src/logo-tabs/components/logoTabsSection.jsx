import React, { useState } from 'react';
import { useBlockProps, RichText, InnerBlocks } from '@wordpress/block-editor';
import { TabPanel } from '@wordpress/components';

export const LogoTabsSection = ({ attributes, setAttributes, isEditor }) => {
    const LOGO_TABS = [
        { name: 'primary', title: 'Primary Logo' },
        { name: 'reversed', title: 'Reversed' },
        { name: 'icon', title: 'Icon' },
        { name: 'wordmark', title: 'Wordmark' },
        { name: 'variations', title: 'Color Variations' }
    ];

    const onSelect = (tabName) => {
        console.log('Selected tab:', tabName);
    };

    return (
        <div {...useBlockProps()}>
            <div className="logo-tabs-container">
                <TabPanel
                    className="logo-tabs-panel"
                    activeClass="active-tab"
                    tabs={LOGO_TABS}
                    onSelect={onSelect}
                >
                    {(tab) => (
                        <div className="tab-content">
                            <InnerBlocks
                                template={[
                                    ['core/heading', { level: 3, content: tab.title }],
                                    ['core/paragraph', { placeholder: 'Add description...' }],
                                    ['core/image', { className: 'logo-preview' }],
                                    ['core/list', { placeholder: 'Add usage guidelines...' }]
                                ]}
                                templateLock={false}
                            />
                        </div>
                    )}
                </TabPanel>
            </div>
        </div>
    );
};

export default LogoTabsSection;