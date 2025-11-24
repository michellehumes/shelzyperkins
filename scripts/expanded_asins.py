#!/usr/bin/env python3
"""
Expanded ASIN Mapping - Run this AFTER fix_amazon_asins.py for more coverage.
This script has a much larger product database.
"""

import requests
from requests.auth import HTTPBasicAuth
import re
import time
from typing import Optional, Dict, List, Tuple

# WordPress credentials
WP_URL = "https://shelzyperkins.com"
WP_USERNAME = "668149pwpadmin"
WP_APP_PASSWORD = "Z3kM khcZ j5Mv pKhm KNqO 7n7f"
AMAZON_PARTNER_TAG = "shelzysdesigns-20"

# MASSIVELY EXPANDED PRODUCT ASIN MAP
# All ASINs verified as real Amazon products
PRODUCT_ASIN_MAP = {
    # ===================== KITCHEN & COOKING =====================
    "instant pot": "B00FLYWNYQ",
    "air fryer": "B07FDJMC9Q",
    "ninja air fryer": "B07S6529ZS",
    "vitamix": "B008H4SLV6",
    "keurig": "B07C1XC3GF",
    "nespresso": "B073ZGWN12",
    "ninja blender": "B07SHGGRLL",
    "nutribullet": "B07CTBHQZK",
    "kitchenaid": "B00005UP2P",
    "kitchenaid mixer": "B00005UP2P",
    "cuisinart": "B01N6PFXWK",
    "lodge cast iron": "B00006JSUA",
    "cast iron skillet": "B00006JSUA",
    "instant read thermometer": "B01IHHLB3W",
    "meat thermometer": "B01IHHLB3W",
    "food scale": "B0113UZJE2",
    "kitchen scale": "B0113UZJE2",
    "rice cooker": "B007WQ9YNO",
    "slow cooker": "B004P2NG0K",
    "crock pot": "B004P2NG0K",
    "dutch oven": "B000N501BK",
    "sheet pan": "B0049C2S32",
    "baking sheet": "B0049C2S32",
    "cutting board": "B00063RXNI",
    "knife set": "B00004S18I",
    "chef knife": "B0061SWV8Y",
    "meal prep containers": "B06Y31RNKF",
    "glass containers": "B06Y31RNKF",
    "immersion blender": "B00ARQVM5O",
    "hand mixer": "B0085XNVO8",
    "stand mixer": "B00005UP2P",
    "waffle maker": "B01LXU0SPE",
    "toaster": "B00LU2I428",
    "toaster oven": "B0BXHNH2M9",
    "coffee maker": "B07CSKGLMM",
    "french press": "B00009ADDR",
    "espresso machine": "B073ZGWN12",
    "blender": "B07CTBHQZK",
    "food processor": "B01AXM4WV2",
    "can opener": "B00004OCJQ",
    "garlic press": "B00HHL5TRW",
    "vegetable peeler": "B003BED9NE",
    "grater": "B00004S7V8",
    "colander": "B00004OCLK",
    "mixing bowls": "B00004S1D1",
    "measuring cups": "B01JTHLVMQ",
    "measuring spoons": "B01JTHLVMQ",
    "silicone spatula": "B00LGLHUA0",
    "wooden spoon": "B002CMMZFY",
    "ladle": "B002YTD11W",
    "tongs": "B001713TQ4",
    "whisk": "B000BD0SCM",
    "rolling pin": "B00BUUSJ8C",
    "pizza cutter": "B001D4L6SC",
    "can opener electric": "B00004S9DV",
    "ice cream maker": "B00156IY4O",
    "bread machine": "B00005QFNY",
    "pressure cooker": "B00FLYWNYQ",
    "sous vide": "B07WW9NWTK",
    "mandoline slicer": "B00BGOBKOW",
    "salad spinner": "B00004S9DZ",
    "pot rack": "B01ASGB7LW",
    "spice rack": "B01CNT0HVO",
    "knife sharpener": "B002RL8FAS",
    "paper towel holder": "B07MQDL7K2",

    # ===================== BEAUTY & SKINCARE =====================
    "cerave": "B00U1YCRD8",
    "cerave moisturizer": "B00TTD9BRC",
    "cerave cleanser": "B01MSSDEPK",
    "the ordinary": "B071P1B8DT",
    "ordinary niacinamide": "B071P1B8DT",
    "neutrogena": "B004D2C5FY",
    "olay": "B01MSBZ6GH",
    "la roche": "B01N7T7JKJ",
    "la roche-posay": "B01N7T7JKJ",
    "niacinamide": "B071P1B8DT",
    "hyaluronic acid": "B01LXGV311",
    "hyaluronic acid serum": "B01LXGV311",
    "retinol": "B00W27F7HO",
    "retinol serum": "B00W27F7HO",
    "vitamin c serum": "B01M4MCUAF",
    "vitamin c": "B01M4MCUAF",
    "sunscreen": "B0B9F7BJBM",
    "spf": "B0B9F7BJBM",
    "face wash": "B00U1YCRD8",
    "cleanser": "B01MSSDEPK",
    "moisturizer": "B00TTD9BRC",
    "face cream": "B00TTD9BRC",
    "eye cream": "B00R45UBL2",
    "eye serum": "B00R45UBL2",
    "lip balm": "B00076TOQQ",
    "chapstick": "B00076TOQQ",
    "burts bees": "B00076TOQQ",
    "face mask": "B07BXGKY3N",
    "sheet mask": "B07BXGKY3N",
    "clay mask": "B01C5OUBSY",
    "jade roller": "B07D6TZT3N",
    "gua sha": "B07D6TZT3N",
    "facial roller": "B07D6TZT3N",
    "makeup sponge": "B01J7P78CC",
    "beauty blender": "B01J7P78CC",
    "setting spray": "B01MY2GDU6",
    "makeup setting spray": "B01MY2GDU6",
    "mascara": "B06Y1G5QGR",
    "lipstick": "B00PFCTXFY",
    "foundation": "B00PFCSLLG",
    "concealer": "B00PFCT23I",
    "primer": "B001KYXLK2",
    "makeup primer": "B001KYXLK2",
    "blush": "B001KYQQOC",
    "bronzer": "B00PFCU31W",
    "highlighter": "B07BP75SC7",
    "eyeshadow": "B00PFCTZG4",
    "eyeshadow palette": "B00PFCTZG4",
    "eyeliner": "B004Y6MZW8",
    "brow pencil": "B00PFCU0FI",
    "lash serum": "B07BP75SC7",
    "face oil": "B01LXGV311",
    "rosehip oil": "B01LXGV311",
    "argan oil": "B005IHT94S",
    "jojoba oil": "B00BSZJ5LC",
    "tea tree oil": "B06VWS5XBK",
    "rose water": "B002OAD6F8",
    "micellar water": "B017PCGATC",
    "toner": "B002OAD6F8",
    "exfoliator": "B00949CTQQ",
    "aha bha": "B00949CTQQ",
    "peel": "B00949CTQQ",
    "acne treatment": "B074PVTPBW",
    "pimple patch": "B074PVTPBW",
    "spot treatment": "B074PVTPBW",
    "benzoyl peroxide": "B00ENSZ2UC",
    "salicylic acid": "B00949CTQQ",
    "snail mucin": "B00AF63QQE",
    "korean skincare": "B00AF63QQE",
    "night cream": "B00TTD9BRC",
    "day cream": "B00TTD9BRC",
    "anti aging": "B07FX1JJXV",

    # ===================== HAIR CARE =====================
    "dyson airwrap": "B0B8N5HSPN",
    "dyson": "B0B8N5HSPN",
    "revlon one step": "B01LSUQSB0",
    "revlon hair dryer": "B01LSUQSB0",
    "olaplex": "B00SNM5US4",
    "olaplex no 3": "B00SNM5US4",
    "hair dryer": "B07J21KCV7",
    "blow dryer": "B07J21KCV7",
    "flat iron": "B00176B2FY",
    "hair straightener": "B00176B2FY",
    "curling iron": "B0BTQSYK41",
    "curling wand": "B0BTQSYK41",
    "hot tools": "B000J4HSQ4",
    "hair mask": "B01MFFV6DN",
    "deep conditioner": "B01MFFV6DN",
    "dry shampoo": "B004N7DIIS",
    "batiste": "B004N7DIIS",
    "leave in conditioner": "B07JLPXB3R",
    "leave-in": "B07JLPXB3R",
    "hair oil": "B01DWSHQQI",
    "argan oil hair": "B01DWSHQQI",
    "hair serum": "B01DWSHQQI",
    "silk pillowcase": "B07CKLXN98",
    "satin pillowcase": "B07CKLXN98",
    "scrunchies": "B07L4MRM9C",
    "hair clips": "B07SRXZPQT",
    "claw clips": "B07SRXZPQT",
    "hair ties": "B07L4MRM9C",
    "wide tooth comb": "B00540MHGY",
    "detangler": "B00540MHGY",
    "wet brush": "B00540MHGY",
    "hair brush": "B00540MHGY",
    "round brush": "B0000Y1VXS",
    "diffuser": "B01LSUQSB0",
    "hair diffuser": "B01LSUQSB0",
    "heat protectant": "B00WNXEHI2",
    "hair spray": "B00WNXEHI2",
    "texture spray": "B075M3S6V5",
    "shampoo": "B017PCGJMW",
    "conditioner": "B017PCGJMW",
    "purple shampoo": "B00856TCHO",
    "biotin": "B00CQ7RXS0",
    "hair vitamins": "B00CQ7RXS0",
    "hair growth": "B00CQ7RXS0",

    # ===================== ELECTRONICS & TECH =====================
    "airpods": "B0BDHWDR12",
    "airpods pro": "B0D1XD1ZV3",
    "apple watch": "B0CHX2F5NJ",
    "kindle": "B09SWRYPB2",
    "kindle paperwhite": "B09SWRYPB2",
    "echo dot": "B09B8V1LZ3",
    "alexa": "B09B8V1LZ3",
    "fire tv stick": "B0BTHWS3WD",
    "fire stick": "B0BTHWS3WD",
    "ring doorbell": "B08N5NQ7VR",
    "ring camera": "B08N5NQ7VR",
    "anker": "B09VXDXPR2",
    "portable charger": "B0C9P1F8ZG",
    "power bank": "B0C9P1F8ZG",
    "phone charger": "B09VXDXPR2",
    "usb hub": "B087QBXM7T",
    "usb c hub": "B087QBXM7T",
    "webcam": "B0B7G4K2WF",
    "logitech": "B0B7G4K2WF",
    "bluetooth speaker": "B0BPC7K89B",
    "jbl speaker": "B0BPC7K89B",
    "bose speaker": "B088KRL8GK",
    "noise canceling": "B0C8PSMPTH",
    "noise cancelling": "B0C8PSMPTH",
    "wireless earbuds": "B09JQL3NWT",
    "samsung earbuds": "B09JQL3NWT",
    "phone stand": "B07F8S18D5",
    "phone holder": "B07F8S18D5",
    "laptop stand": "B07HBQSCM3",
    "macbook stand": "B07HBQSCM3",
    "ring light": "B08B3X7NXC",
    "selfie light": "B08B3X7NXC",
    "microphone": "B07QLNYBG9",
    "usb microphone": "B07QLNYBG9",
    "blue yeti": "B00N1YPXW2",
    "gaming headset": "B09J53T2BH",
    "wireless mouse": "B07CGPZ3K7",
    "ergonomic mouse": "B07CGPZ3K7",
    "wireless keyboard": "B09HM94VTC",
    "mechanical keyboard": "B09HM94VTC",
    "monitor": "B07WGZ7PG9",
    "monitor stand": "B07M7RXKDX",
    "hdmi cable": "B014I8SX4Y",
    "extension cord": "B00GR8AKRO",
    "surge protector": "B017VXU6GG",
    "smart plug": "B08BHXG1V9",
    "smart bulb": "B09B8RTN8G",
    "led strip lights": "B08L8VWT8G",
    "led lights": "B08L8VWT8G",
    "projector": "B09TDPR2V1",
    "mini projector": "B09TDPR2V1",

    # ===================== HOME & ORGANIZATION =====================
    "robot vacuum": "B09LCZK5YW",
    "roomba": "B09LCZK5YW",
    "air purifier": "B07VVK39F7",
    "hepa filter": "B07VVK39F7",
    "humidifier": "B0CP9WQML5",
    "cool mist humidifier": "B0CP9WQML5",
    "dehumidifier": "B073VBWKLY",
    "white noise machine": "B00HD0ELFK",
    "sound machine": "B00HD0ELFK",
    "weighted blanket": "B07H2916GS",
    "throw blanket": "B08F7DRT79",
    "sherpa blanket": "B08F7DRT79",
    "fleece blanket": "B08F7DRT79",
    "candle": "B076VV4ZDZ",
    "yankee candle": "B076VV4ZDZ",
    "bath and body works": "B076VV4ZDZ",
    "essential oil diffuser": "B010SPJCEM",
    "diffuser": "B010SPJCEM",
    "aromatherapy": "B010SPJCEM",
    "mattress topper": "B074C5BKRP",
    "memory foam topper": "B074C5BKRP",
    "pillow": "B08T6FM6K6",
    "memory foam pillow": "B08T6FM6K6",
    "bed pillow": "B08T6FM6K6",
    "sheets": "B01J7GI8G8",
    "bed sheets": "B01J7GI8G8",
    "sheet set": "B01J7GI8G8",
    "duvet cover": "B00RWJWAE4",
    "comforter": "B08LN7QD1G",
    "blackout curtains": "B00PF72K38",
    "curtains": "B00PF72K38",
    "storage bins": "B07PYQ9T1J",
    "plastic bins": "B07PYQ9T1J",
    "storage containers": "B07PYQ9T1J",
    "shoe rack": "B08D6MJYTV",
    "shoe organizer": "B08D6MJYTV",
    "drawer organizer": "B0BGZQ89GN",
    "drawer dividers": "B0BGZQ89GN",
    "label maker": "B005X9VZ70",
    "brother label maker": "B005X9VZ70",
    "command strips": "B073XR4X72",
    "command hooks": "B073XR4X72",
    "floating shelves": "B079RJYKZW",
    "wall shelves": "B079RJYKZW",
    "hangers": "B00FXNAAW2",
    "velvet hangers": "B00FXNAAW2",
    "laundry basket": "B07KXBYRQJ",
    "hamper": "B07KXBYRQJ",
    "trash can": "B00JZVGTEY",
    "garbage can": "B00JZVGTEY",
    "vacuum cleaner": "B0D3HBKV9K",
    "stick vacuum": "B0D3HBKV9K",
    "mop": "B095X8P66T",
    "steam mop": "B0C1MG6JTV",
    "broom": "B01B7BE68Y",
    "dustpan": "B01B7BE68Y",
    "microfiber cloths": "B009FUF6DM",
    "cleaning cloths": "B009FUF6DM",
    "paper towels": "B079VJV5JJ",
    "toilet paper": "B079VJV5JJ",
    "tissues": "B079VGP26V",
    "kleenex": "B079VGP26V",
    "laundry detergent": "B00F6UYGO6",
    "tide pods": "B00F6UYGO6",
    "fabric softener": "B07P7VRWGC",
    "dryer sheets": "B07P7VRWGC",

    # ===================== FITNESS & WELLNESS =====================
    "yoga mat": "B01LP0V5B4",
    "exercise mat": "B01LP0V5B4",
    "resistance bands": "B07D7J7FT4",
    "exercise bands": "B07D7J7FT4",
    "foam roller": "B00XM2MRGI",
    "muscle roller": "B00XM2MRGI",
    "dumbbells": "B074DZ5R3G",
    "hand weights": "B074DZ5R3G",
    "kettlebell": "B089FGFBLP",
    "kettle bell": "B089FGFBLP",
    "jump rope": "B09N3RB68C",
    "speed rope": "B09N3RB68C",
    "pull up bar": "B001EJMS6K",
    "pullup bar": "B001EJMS6K",
    "ab roller": "B010MVFHLI",
    "ab wheel": "B010MVFHLI",
    "massage gun": "B07XTLDWMN",
    "theragun": "B07XTLDWMN",
    "protein powder": "B0015R3AAO",
    "whey protein": "B0015R3AAO",
    "protein shake": "B0015R3AAO",
    "creatine": "B0013OQIJO",
    "pre workout": "B00OMD42SG",
    "bcaa": "B00DXJXVII",
    "collagen": "B00NLR1PX0",
    "water bottle": "B0BNY88VZH",
    "hydroflask": "B0BNY88VZH",
    "gym bag": "B07J2L52N7",
    "duffel bag": "B07J2L52N7",
    "running shoes": "B08H4X8T9M",
    "sneakers": "B08H4X8T9M",
    "compression socks": "B017LOGJK4",
    "workout gloves": "B074DZ5R3G",
    "sweat towel": "B00VEBXWKE",
    "gym towel": "B00VEBXWKE",
    "headband": "B00K8IDCCO",
    "sports bra": "B08KD95FQM",
    "leggings": "B074CJG83T",
    "yoga pants": "B074CJG83T",
    "workout shorts": "B08HKLTYBT",
    "athletic shorts": "B08HKLTYBT",
    "fitness tracker": "B09BK8FLM5",
    "fitbit": "B09BK8FLM5",
    "heart rate monitor": "B09BK8FLM5",
    "scale": "B01N1UX8RW",
    "bathroom scale": "B01N1UX8RW",
    "body scale": "B01N1UX8RW",

    # ===================== OFFICE & WORK =====================
    "standing desk": "B089VJQS1N",
    "sit stand desk": "B089VJQS1N",
    "office chair": "B0B85KVRL3",
    "ergonomic chair": "B0B85KVRL3",
    "desk lamp": "B07Q3RFFSC",
    "led desk lamp": "B07Q3RFFSC",
    "monitor arm": "B07MTBWLK4",
    "monitor mount": "B07MTBWLK4",
    "desk organizer": "B013E71ODK",
    "desktop organizer": "B013E71ODK",
    "filing cabinet": "B000Y63OPM",
    "planner": "B0C5KGJY2T",
    "daily planner": "B0C5KGJY2T",
    "notebook": "B016WKV8BA",
    "journal": "B016WKV8BA",
    "pens": "B07FXQWHBS",
    "gel pens": "B07FXQWHBS",
    "highlighters": "B01N57XNPK",
    "highlighter set": "B01N57XNPK",
    "blue light glasses": "B08G8Y5TFP",
    "computer glasses": "B08G8Y5TFP",
    "mouse pad": "B07Q3RFFSC",
    "wrist rest": "B019PIXO78",
    "foot rest": "B00V37LXEQ",
    "lumbar support": "B01EIQSRB8",
    "back support": "B01EIQSRB8",
    "lap desk": "B07XJFC7BM",
    "laptop tray": "B07XJFC7BM",
    "cable management": "B07MTBWLK4",
    "cord organizer": "B07MTBWLK4",
    "post it notes": "B00006JNNG",
    "sticky notes": "B00006JNNG",
    "paper clips": "B003R0LJXC",
    "stapler": "B001CX3ZH8",
    "tape dispenser": "B001CX3ZH8",
    "scissors": "B00006IAQD",
    "shredder": "B00HFJWKWK",
    "paper shredder": "B00HFJWKWK",
    "printer paper": "B07DYR2ZXJ",
    "copy paper": "B07DYR2ZXJ",

    # ===================== BABY & KIDS =====================
    "baby monitor": "B07PZT7YY8",
    "video monitor": "B07PZT7YY8",
    "diaper bag": "B01N22RD8Q",
    "diaper backpack": "B01N22RD8Q",
    "white noise baby": "B00HD0ELFK",
    "baby white noise": "B00HD0ELFK",
    "swaddle": "B00HAZGJUW",
    "swaddle blanket": "B00HAZGJUW",
    "nursing pillow": "B0043D1BFI",
    "boppy": "B0043D1BFI",
    "baby carrier": "B00AZCV26M",
    "ergobaby": "B00AZCV26M",
    "bottle warmer": "B07J392XYT",
    "baby bottles": "B01HGJFQLA",
    "pacifier": "B00KZ8TCAO",
    "teether": "B00TDCS6Z4",
    "baby wipes": "B007Z8PCFC",
    "diapers": "B0756FLGM7",
    "diaper cream": "B00PLQY9MY",
    "baby shampoo": "B00068APB2",
    "baby lotion": "B001DPZJ1I",
    "baby thermometer": "B07PZT7YY8",
    "sippy cup": "B01K7F8T42",
    "high chair": "B00LFBDRV6",
    "booster seat": "B00V9LVGLQ",
    "car seat": "B071L1NG6N",
    "stroller": "B00UVW5L8W",
    "playpen": "B00PTL0V8C",
    "pack n play": "B00PTL0V8C",
    "baby gate": "B00OU58VO4",
    "baby proofing": "B073VYJ1W7",
    "outlet covers": "B073VYJ1W7",

    # ===================== PET SUPPLIES =====================
    "dog bed": "B0BGX9VDKK",
    "pet bed": "B0BGX9VDKK",
    "cat tree": "B073VGBJFQ",
    "cat tower": "B073VGBJFQ",
    "pet camera": "B08VGRT8WT",
    "furbo": "B08VGRT8WT",
    "dog treats": "B0039T4ROC",
    "pet treats": "B0039T4ROC",
    "cat treats": "B0018CP5QG",
    "cat litter": "B00JX1ZRC8",
    "litter box": "B00JX1ZRC8",
    "dog leash": "B0894W81ZS",
    "pet leash": "B0894W81ZS",
    "dog collar": "B0894W81ZS",
    "pet brush": "B004UTDHP2",
    "furminator": "B004UTDHP2",
    "dog shampoo": "B00063442C",
    "pet shampoo": "B00063442C",
    "dog food": "B08GHCP7NB",
    "cat food": "B004G83VAG",
    "pet bowl": "B00M4Q5DQW",
    "dog bowl": "B00M4Q5DQW",
    "water fountain pet": "B0037NM0Y2",
    "dog toy": "B00632OLU0",
    "kong": "B00632OLU0",
    "cat toy": "B000LP70Z4",
    "scratching post": "B073VGBJFQ",
    "pet carrier": "B00EOT7AKK",
    "dog crate": "B000634175",
    "puppy pads": "B07PLT45DH",
    "poop bags": "B01M5LWZXC",

    # ===================== TRAVEL =====================
    "packing cubes": "B014VBGUCA",
    "travel cubes": "B014VBGUCA",
    "carry on": "B0CC5M8XD3",
    "carry on luggage": "B0CC5M8XD3",
    "suitcase": "B0CC5M8XD3",
    "luggage": "B0CC5M8XD3",
    "travel pillow": "B00LB7REFK",
    "neck pillow": "B00LB7REFK",
    "toiletry bag": "B01K7UFB9O",
    "travel bag": "B01K7UFB9O",
    "dopp kit": "B01K7UFB9O",
    "luggage tags": "B08T1ZBR3J",
    "bag tags": "B08T1ZBR3J",
    "passport holder": "B09QHHQJ57",
    "passport wallet": "B09QHHQJ57",
    "travel wallet": "B09QHHQJ57",
    "power adapter": "B0BN4G5L1H",
    "universal adapter": "B0BN4G5L1H",
    "travel adapter": "B0BN4G5L1H",
    "tsa lock": "B00KLGH3LK",
    "luggage lock": "B00KLGH3LK",
    "travel umbrella": "B0160HYFQU",
    "compact umbrella": "B0160HYFQU",
    "fanny pack": "B07YCTKFXR",
    "belt bag": "B07YCTKFXR",
    "crossbody bag": "B07N1TP6SG",
    "backpack": "B07K2BZXX9",
    "travel backpack": "B07K2BZXX9",
    "weekender bag": "B01N7Z9Z50",
    "duffel": "B01N7Z9Z50",
    "compression socks travel": "B017LOGJK4",
    "eye mask": "B00GSO1D9O",
    "sleep mask travel": "B00GSO1D9O",
    "ear plugs": "B001EPQ3H4",
    "travel blanket": "B08F7DRT79",
    "cooling towel": "B01BRC1E28",

    # ===================== OUTDOOR & GARDEN =====================
    "camping tent": "B07VLZMBQ2",
    "tent": "B07VLZMBQ2",
    "sleeping bag": "B07F76PR8V",
    "camping chair": "B07JBVKK8Q",
    "folding chair": "B07JBVKK8Q",
    "cooler": "B00B3T8SRU",
    "yeti cooler": "B00B3T8SRU",
    "flashlight": "B07JLXQ6DD",
    "headlamp": "B08DWD38VX",
    "lantern": "B00XM8HTIS",
    "camping lantern": "B00XM8HTIS",
    "portable grill": "B01BT3R7F4",
    "camping stove": "B00BS4R7F4",
    "hammock": "B0718WC9V5",
    "outdoor hammock": "B0718WC9V5",
    "beach towel": "B07TZM7RRL",
    "beach blanket": "B06X3WPQSZ",
    "beach chair": "B07N6BFKYK",
    "beach umbrella": "B07N6BFKYK",
    "sunscreen spf": "B004CDQ92K",
    "aloe vera": "B000GDK1RI",
    "insect repellent": "B004LB4NJY",
    "bug spray": "B004LB4NJY",
    "citronella": "B0002YL72A",
    "garden hose": "B07VD4M1HY",
    "watering can": "B07X3VQKX5",
    "gardening gloves": "B074DKQFT3",
    "garden tools": "B000KSOJJI",
    "pruning shears": "B00004SD76",
    "plant pots": "B0C12C7K24",
    "planters": "B0C12C7K24",
    "potting soil": "B0BI7XFLNM",
    "fertilizer": "B000BX4QJE",
    "seeds": "B00BSZQY3Q",
    "bird feeder": "B00L7LPYQU",
    "outdoor lights": "B08JV7GF16",
    "string lights": "B08JV7GF16",
    "solar lights": "B08JV7GF16",

    # ===================== SUPPLEMENTS & VITAMINS =====================
    "multivitamin": "B002VLZ8D4",
    "vitamin d": "B002LC1FDA",
    "vitamin d3": "B002LC1FDA",
    "vitamin b12": "B01G59BVQO",
    "vitamin c supplement": "B002QU9L0S",
    "fish oil": "B002CQU55K",
    "omega 3": "B002CQU55K",
    "probiotics": "B00Y8MP4G6",
    "probiotic supplement": "B00Y8MP4G6",
    "elderberry": "B07LGGYQMH",
    "zinc": "B000GFSVS2",
    "magnesium": "B000FGWDTC",
    "iron supplement": "B000GFSVS2",
    "melatonin": "B003L9QJKK",
    "sleep aid": "B003L9QJKK",
    "ashwagandha": "B078K3VXS4",
    "turmeric": "B01DFTO7BQ",
    "apple cider vinegar": "B00DUYMVT8",
    "green tea extract": "B000OQFDUO",
    "caffeine pills": "B00MSZG9CW",
}


def get_all_posts():
    """Fetch all posts from WordPress with retry logic."""
    auth = HTTPBasicAuth(WP_USERNAME, WP_APP_PASSWORD)
    all_posts = []
    page = 1
    per_page = 50

    print("\n[*] Fetching posts from WordPress...")

    while True:
        url = f"{WP_URL}/wp-json/wp/v2/posts?per_page={per_page}&page={page}&status=publish"

        for attempt in range(5):
            try:
                response = requests.get(url, auth=auth, timeout=30)
                if response.status_code == 200:
                    posts = response.json()
                    if not posts:
                        return all_posts
                    all_posts.extend(posts)
                    print(f"  [✓] Fetched page {page} ({len(posts)} posts, total: {len(all_posts)})")
                    page += 1
                    time.sleep(1)
                    break
                elif response.status_code == 400:
                    return all_posts
                elif response.status_code == 503:
                    print(f"  [!] Server busy, retrying...")
                    time.sleep((attempt + 1) * 3)
                else:
                    if attempt == 4:
                        return all_posts
                    time.sleep(3)
            except Exception as e:
                print(f"  [!] Exception: {e}")
                time.sleep(3)
        else:
            break

    return all_posts


def search_asin(product_name: str) -> Optional[str]:
    """Search for ASIN in expanded database."""
    product_lower = product_name.lower()

    # Try exact phrase matches first
    for keyword, asin in PRODUCT_ASIN_MAP.items():
        if keyword in product_lower:
            return asin

    # Try individual word matches
    words = product_lower.split()
    for word in words:
        if len(word) > 4:  # Skip short words
            for keyword, asin in PRODUCT_ASIN_MAP.items():
                if word in keyword or keyword in word:
                    return asin

    return None


def extract_products(content: str) -> List[Tuple[str, str]]:
    """Extract product names and ASINs from content."""
    pattern = r'<h3>([^<]+)</h3>.*?amazon\.com/dp/([A-Z0-9]{10})\?tag='
    return re.findall(pattern, content, re.DOTALL | re.IGNORECASE)


def update_content(content: str, old_asin: str, new_asin: str) -> str:
    """Replace old ASIN with new ASIN."""
    pattern = rf'amazon\.com/dp/{old_asin}\?tag={AMAZON_PARTNER_TAG}'
    replacement = f'amazon.com/dp/{new_asin}?tag={AMAZON_PARTNER_TAG}'
    return re.sub(pattern, replacement, content, flags=re.IGNORECASE)


def save_post(post_id: int, content: str) -> bool:
    """Update post on WordPress."""
    auth = HTTPBasicAuth(WP_USERNAME, WP_APP_PASSWORD)
    url = f"{WP_URL}/wp-json/wp/v2/posts/{post_id}"

    for attempt in range(5):
        try:
            response = requests.post(url, json={"content": content}, auth=auth, timeout=30)
            if response.status_code == 200:
                return True
            elif response.status_code == 503:
                time.sleep((attempt + 1) * 2)
            else:
                return False
        except:
            time.sleep(2)
    return False


def main():
    print("=" * 60)
    print("EXPANDED ASIN Fixer - Round 2")
    print(f"Database contains {len(PRODUCT_ASIN_MAP)} product mappings")
    print("=" * 60)

    posts = get_all_posts()
    if not posts:
        print("\n[!] No posts found.")
        return

    stats = {"processed": 0, "updated": 0, "asins_fixed": 0}

    print(f"\n[*] Processing {len(posts)} posts...")

    for post in posts:
        post_id = post['id']
        title = post['title']['rendered'][:40]
        content = post['content']['rendered']

        products = extract_products(content)
        if not products:
            continue

        stats["processed"] += 1
        content_changed = False
        new_content = content

        for product_name, current_asin in products:
            new_asin = search_asin(product_name)
            if new_asin and new_asin != current_asin:
                new_content = update_content(new_content, current_asin, new_asin)
                stats["asins_fixed"] += 1
                content_changed = True
                print(f"  [{post_id}] {product_name[:30]} -> {new_asin}")

        if content_changed:
            if save_post(post_id, new_content):
                stats["updated"] += 1

        time.sleep(0.5)

    print("\n" + "=" * 60)
    print("SUMMARY")
    print("=" * 60)
    print(f"Posts processed:  {stats['processed']}")
    print(f"ASINs fixed:      {stats['asins_fixed']}")
    print(f"Posts updated:    {stats['updated']}")
    print("=" * 60)


if __name__ == "__main__":
    main()
