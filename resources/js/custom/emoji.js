import twemoji from 'twemoji';

/**
 * Convert emoji to image HTML
 * @param {string} emoji - The emoji to convert
 * @returns {string} HTML img tag wrapped in .emoji-text span
 */
function emojiToImage(emoji) {
    if (!emoji) return '';

    const parsed = twemoji.parse(emoji, {
        folder: 'svg',
        ext: '.svg',
        base: 'https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/',
        className: 'emoji'
    });

    return `<span class="emoji-text">${parsed}</span>`;
}

/**
 * Initialize emoji rendering using twemoji
 * Converts Unicode emoji (especially flags) to SVG images
 * Only targets specific elements to avoid loading all emojis
 */
function initEmojiRendering() {
    // Parse only specific emoji containers instead of entire document
    const emojiContainers = document.querySelectorAll('.emoji-text, [data-emoji], .country-flag');

    emojiContainers.forEach(element => {
        twemoji.parse(element, {
            folder: 'svg',
            ext: '.svg',
            base: 'https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/',
            className: 'emoji'
        });
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
                // Only parse if the node itself or its children contain emoji elements
                if (node.matches && node.matches('.emoji-text, [data-emoji], .country-flag')) {
                    twemoji.parse(node, {
                        folder: 'svg',
                        ext: '.svg',
                        base: 'https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/',
                        className: 'emoji'
                    });
                } else if (node.querySelectorAll) {
                    // Check for emoji elements within the added node
                    const emojiElements = node.querySelectorAll('.emoji-text, [data-emoji], .country-flag');
                    emojiElements.forEach(element => {
                        twemoji.parse(element, {
                            folder: 'svg',
                            ext: '.svg',
                            base: 'https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/',
                            className: 'emoji'
                        });
                    });
                }
            }
        });
    });
});

// Start observing
observer.observe(document.body, {
    childList: true,
    subtree: true
});

// Make functions and twemoji available globally
window.emojiToImage = emojiToImage;
window.initEmojiRendering = initEmojiRendering;
window.twemoji = twemoji;

export { initEmojiRendering, emojiToImage };
