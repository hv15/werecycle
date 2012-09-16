var selectables = [
    {"name":"Common Recyclables",
     "types":[
        {"name":"Aerosol cans","id":29},
        {"name":"Alumimium foil","id":41},
        {"name":"Cardboard","id":2},
        {"name":"Cartons","id":17},
        {"name":"Glass bottles & jars","id":6},
    	{"name":"Household metal packaging","id":1},
        {"name":"Paper","id":7},
        {"name":"Plastic bottles","id":16},
        {"name":"Yellow Pages","id":13}
    	]
    },
    {"name":"Electronics",
     "types":[
        {"name":"Batteries","id":32},
        {"name":"Car & Engine batteries","id":38},
        {"name":"Energy saving lightbulbs &amp; Flourescent tubes","id":26},
        {"name":"Fridge & Freezer","id":40},
        {"name":"Large Electrical Appliances","id":30},
        {"name":"Small Electrical Appliances","id":45},
        {"name":"TVs & Monitors","id":46}
        ]
    },
    {"name":"Building/Garden Materials",
     "types":[
        {"name":"Garden Waste","id":4},
        {"name":"Hard-Core & Rubble","id":33},
        {"name":"Scrap Metals","id":9},
        {"name":"Wood & Timber","id":12}
        ]
    },
    {"name":"Various Other Recyclables",
     "types":[
        {"name":"Books","id":18},
        {"name":"Clothing","id":11},
        {"name":"DVDs, CDs, Videos & Tapes","id":25},
        {"name":"Engine Oil","id":3},
        {"name":"Furniture","id":23},
        {"name":"Mixed textiles & clothes","id":49},
        {"name":"Mobile Phones","id":49},
        {"name":"Paint","id":27},
        {"name":"Printer Cartridges","id":34},
        {"name":"Shoes","id":43},
        {"name":"Spectacles","id":51},
        {"name":"Tyres","id":22}
        ]
    }
]

/*

The above categorisation is based on the stuff below:

Common
1 Household metal packaging
2 cardboard
6 glass bottles & jars
7 paper
13 yellow pages
16 plastic bottles
17 cartons
29 aerosol cans
41 aluminium foil

electric (all RC)
26: energy saving light bulbs & fluorescent tubes
30: large electrical appliances
32: batteries
38: car & engine batteries
40: fridge & freezers
45: small appliances
46: TVs & monitors

building/garden materials
04: garden waste
09: scrap metals
12: wood & timber
33:hard-core & rubble

various //other
3: engine oil - no DB
11: clothing
18: books    - no Craig
22: tyres      - no Kin
23: furniture - no Kin
25: DVDs, CDs, videos & tapes
27: paint
34: printer cartridges
42: mobile phones
43: shoes    - no Sea/Sight
49: mixed textiles & clothes
51: spectacles*

/