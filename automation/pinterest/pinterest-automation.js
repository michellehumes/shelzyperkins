/**
 * Pinterest Automation for ShelzyPerkins
 *
 * Automated pin creation and scheduling
 * Use with Zapier, Make (Integromat), or Buffer
 */

const PINTEREST_CONFIG = {
    boardIds: {
        deals: 'BOARD_ID_DEALS',
        beauty: 'BOARD_ID_BEAUTY',
        home: 'BOARD_ID_HOME',
        tech: 'BOARD_ID_TECH',
        fashion: 'BOARD_ID_FASHION',
        kitchen: 'BOARD_ID_KITCHEN',
        giftGuides: 'BOARD_ID_GIFT_GUIDES'
    },
    affiliate: {
        tag: 'shelzysdesigns-20',
        baseUrl: 'https://shelzyperkins.com'
    },
    scheduling: {
        pinsPerDay: 15,
        peakHours: [7, 12, 15, 18, 21], // Best times to post
        timezone: 'America/New_York'
    }
};

/**
 * Generate Pinterest pin description with SEO keywords
 */
function generatePinDescription(product, category) {
    const templates = {
        deals: [
            `{product} is on SALE! 🔥 This {category} find is perfect for anyone looking to save money. Tap to shop this deal before it's gone! #AmazonFinds #DealsOfTheDay #ShopNow`,
            `OMG this {product} is such a steal right now! 💰 One of my favorite {category} picks. Link in bio to shop! #AmazonDeals #MustHave`,
            `Found the BEST deal on {product}! This {category} essential is a game-changer. Swipe up to grab yours! #AmazonPrime #ShelzyFinds`
        ],
        beauty: [
            `This {product} is AMAZING! 💄 A must-have for your {category} routine. Tap to get yours on Amazon! #BeautyFinds #SkincareRoutine #AmazonBeauty`,
            `Can't stop using this {product}! 🌟 The best {category} find I've discovered. Link to shop in bio! #BeautyTips #MakeupLover`,
            `NEW favorite alert! 💕 This {product} is everything. Perfect {category} addition! #AmazonBeautyFinds #GlowUp`
        ],
        home: [
            `This {product} transformed my space! 🏠 Such a great {category} upgrade. Tap to shop! #HomeDecor #AmazonHome #InteriorDesign`,
            `Home upgrade alert! ✨ This {product} is a must-have {category} find. Link in bio! #HomeOrganization #AmazonFinds`,
            `I'm obsessed with this {product}! Perfect for any {category}. Shop the link! #HomeStyling #AmazonHome`
        ],
        tech: [
            `This {product} is a GAME CHANGER! 📱 Best {category} gadget I've found. Tap to get yours! #TechFinds #AmazonTech #Gadgets`,
            `Tech lovers need this {product}! 💻 Amazing {category} find on Amazon. Shop now! #TechDeals #SmartHome`,
            `My new favorite gadget! 🔌 This {product} is perfect {category} tech. Link to shop! #AmazonGadgets #TechLife`
        ]
    };

    const categoryTemplates = templates[category] || templates.deals;
    const template = categoryTemplates[Math.floor(Math.random() * categoryTemplates.length)];

    return template
        .replace(/{product}/g, product.title)
        .replace(/{category}/g, category);
}

/**
 * Generate pin data for a product
 */
function createPinData(product, category) {
    const affiliateUrl = `https://www.amazon.com/dp/${product.asin}?tag=${PINTEREST_CONFIG.affiliate.tag}`;
    const postUrl = `${PINTEREST_CONFIG.affiliate.baseUrl}${product.postUrl}`;

    return {
        board_id: PINTEREST_CONFIG.boardIds[category],
        title: product.title.substring(0, 100), // Pinterest title limit
        description: generatePinDescription(product, category),
        link: postUrl, // Link to blog post (better for SEO)
        media_source: {
            source_type: 'image_url',
            url: product.imageUrl
        },
        alt_text: `${product.title} - Amazon Find by ShelzyPerkins`
    };
}

/**
 * Batch create pins for multiple products
 */
function batchCreatePins(products, category) {
    return products.map(product => createPinData(product, category));
}

/**
 * Generate scheduling times for pins
 */
function generateScheduleTimes(numberOfPins, startDate = new Date()) {
    const schedules = [];
    const peakHours = PINTEREST_CONFIG.scheduling.peakHours;
    let currentDate = new Date(startDate);
    let hourIndex = 0;

    for (let i = 0; i < numberOfPins; i++) {
        const scheduleDate = new Date(currentDate);
        scheduleDate.setHours(peakHours[hourIndex], Math.floor(Math.random() * 30), 0, 0);

        schedules.push({
            pinIndex: i,
            scheduledTime: scheduleDate.toISOString(),
            hour: peakHours[hourIndex]
        });

        hourIndex++;
        if (hourIndex >= peakHours.length) {
            hourIndex = 0;
            currentDate.setDate(currentDate.getDate() + 1);
        }
    }

    return schedules;
}

/**
 * Pin description templates by post type
 */
const PIN_TEMPLATES = {
    dealRoundup: {
        title: "Today's Best Amazon Deals | {date}",
        description: "Don't miss these AMAZING deals! 🔥 I found the best Amazon discounts today - tap to see all the deals before they sell out! #AmazonDeals #DailyDeals #SavingMoney"
    },
    bestOf: {
        title: "Best {category} on Amazon | Top Picks",
        description: "The BEST {category} you can find on Amazon! 🌟 I tested so many products to find these winners. Tap to see my favorites! #BestOf #AmazonFinds #TopPicks"
    },
    giftGuide: {
        title: "{occasion} Gift Guide | Under ${budget}",
        description: "The PERFECT {occasion} gift ideas! 🎁 All under ${budget} and highly rated on Amazon. Tap to shop! #GiftGuide #GiftIdeas #AmazonGifts"
    },
    comparison: {
        title: "{product1} vs {product2} | Which is Better?",
        description: "I compared {product1} and {product2} so you don't have to! 🤔 Tap to see which one wins! #ProductReview #Comparison #AmazonReview"
    }
};

/**
 * Vertical pin image specifications
 */
const PIN_IMAGE_SPECS = {
    width: 1000,
    height: 1500,
    aspectRatio: '2:3',
    format: 'jpg',
    quality: 90,
    textOverlay: {
        title: {
            font: 'Poppins Bold',
            size: 72,
            color: '#FFFFFF',
            position: 'center'
        },
        subtitle: {
            font: 'Poppins Regular',
            size: 36,
            color: '#FFFFFF',
            position: 'below-title'
        }
    },
    brandWatermark: {
        logo: 'shelzyperkins-logo-white.png',
        position: 'bottom-right',
        opacity: 0.8
    }
};

// Export for use in automation tools
module.exports = {
    PINTEREST_CONFIG,
    generatePinDescription,
    createPinData,
    batchCreatePins,
    generateScheduleTimes,
    PIN_TEMPLATES,
    PIN_IMAGE_SPECS
};
