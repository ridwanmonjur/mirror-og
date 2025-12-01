import twemoji from 'twemoji';

/**
 * Convert emoji to image HTML
 * @param {string} emoji - The emoji to convert
 * @returns {string} HTML img tag
 */
function emojiToImage(emoji) {
    if (!emoji) return '';

    const parsed = twemoji.parse(emoji, {
        folder: 'svg',
        ext: '.svg',
        base: 'https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/',
        className: 'emoji'
    });

    return parsed;
}

/**
 * Initialize emoji rendering using twemoji
 * Converts Unicode emoji (especially flags) to SVG images
 */
function initEmojiRendering() {
    // Parse the entire document
    twemoji.parse(document.body, {
        folder: 'svg',
        ext: '.svg',
        base: 'https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/',
        className: 'emoji'
    });
}

// Run on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEmojiRendering);
} else {
    initEmojiRendering();
}

// Re-parse when new content is added (for dynamic content)
const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
            if (node.nodeType === 1) { // Element node
                twemoji.parse(node, {
                    folder: 'svg',
                    ext: '.svg',
                    base: 'https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/',
                    className: 'emoji'
                });
            }
        });
    });
});

// Start observing
observer.observe(document.body, {
    childList: true,
    subtree: true
});

// Make emojiToImage available globally
window.emojiToImage = emojiToImage;

export { initEmojiRendering, emojiToImage };
