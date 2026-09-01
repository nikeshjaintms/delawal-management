<?php

namespace App\Constants;

class ConstructionMaterials
{
    public const CATALOG = [
        'Steel' => [
            'description' => 'TMT Rebars, Binding Wire, Structural Steel',
            'items' => [
                ['label' => '8mm', 'name' => 'TMT Steel 8mm', 'unit' => 'Kg', 'spec' => '8mm'],
                ['label' => '10mm', 'name' => 'TMT Steel 10mm', 'unit' => 'Kg', 'spec' => '10mm'],
                ['label' => '12mm', 'name' => 'TMT Steel 12mm', 'unit' => 'Kg', 'spec' => '12mm'],
                ['label' => '16mm', 'name' => 'TMT Steel 16mm', 'unit' => 'Kg', 'spec' => '16mm'],
                ['label' => '20mm', 'name' => 'TMT Steel 20mm', 'unit' => 'Kg', 'spec' => '20mm'],
                ['label' => '25mm', 'name' => 'TMT Steel 25mm', 'unit' => 'Kg', 'spec' => '25mm'],
                ['label' => '32mm', 'name' => 'TMT Steel 32mm', 'unit' => 'Kg', 'spec' => '32mm'],
                ['label' => 'Binding Wire 18G', 'name' => 'Binding Wire 18G', 'unit' => 'Kg', 'spec' => '18G'],
                ['label' => 'Binding Wire 20G', 'name' => 'Binding Wire 20G', 'unit' => 'Kg', 'spec' => '20G'],
                ['label' => 'MS Angle', 'name' => 'MS Angle', 'unit' => 'Kg', 'spec' => 'MS Angle'],
                ['label' => 'MS Channel', 'name' => 'MS Channel', 'unit' => 'Kg', 'spec' => 'MS Channel'],
                ['label' => 'MS Pipe', 'name' => 'MS Pipe', 'unit' => 'Kg', 'spec' => 'MS Pipe'],
            ]
        ],
        'Cement' => [
            'description' => 'OPC, PPC, White Cement and Plaster',
            'items' => [
                ['label' => '53 Grade (OPC)', 'name' => 'OPC 53 Grade Cement', 'unit' => 'Bags', 'spec' => '53 Grade'],
                ['label' => '43 Grade (OPC)', 'name' => 'OPC 43 Grade Cement', 'unit' => 'Bags', 'spec' => '43 Grade'],
                ['label' => 'PPC', 'name' => 'PPC Pozzolana Cement', 'unit' => 'Bags', 'spec' => 'PPC'],
                ['label' => 'White Cement', 'name' => 'White Cement', 'unit' => 'Bags', 'spec' => 'White Cement'],
                ['label' => 'Gypsum / POP', 'name' => 'Gypsum Plaster / POP', 'unit' => 'Bags', 'spec' => 'Plaster'],
            ]
        ],
        'Sand' => [
            'description' => 'River Sand, M-Sand, Plaster Sand',
            'items' => [
                ['label' => 'River Sand', 'name' => 'River Sand', 'unit' => 'Brass', 'spec' => 'River Sand'],
                ['label' => 'M-Sand', 'name' => 'M-Sand (Crushed)', 'unit' => 'Brass', 'spec' => 'M-Sand'],
                ['label' => 'Stone Dust', 'name' => 'Stone Dust', 'unit' => 'Brass', 'spec' => 'Stone Dust'],
            ]
        ],
        'Aggregate' => [
            'description' => 'Kapchi, Metal, Soling, Rubble',
            'items' => [
                ['label' => '10mm (Kapchi)', 'name' => '10mm Kapchi', 'unit' => 'Brass', 'spec' => '10mm'],
                ['label' => '20mm (Metal)', 'name' => '20mm Metal', 'unit' => 'Brass', 'spec' => '20mm'],
                ['label' => '40mm (Soling)', 'name' => '40mm Metal', 'unit' => 'Brass', 'spec' => '40mm'],
                ['label' => 'Rubble Stone', 'name' => 'Rubble Stone', 'unit' => 'Brass', 'spec' => 'Rubble'],
            ]
        ],
        'Bricks' => [
            'description' => 'Red Clay Bricks, Fly Ash Bricks',
            'items' => [
                ['label' => 'Red Clay Bricks', 'name' => 'Red Clay Bricks', 'unit' => 'Nos', 'spec' => 'Red Clay'],
                ['label' => 'Fly Ash Bricks', 'name' => 'Fly Ash Bricks', 'unit' => 'Nos', 'spec' => 'Fly Ash'],
            ]
        ],
        'Blocks' => [
            'description' => 'AAC Lightweight Blocks, Solid Blocks',
            'items' => [
                ['label' => '4 Inch', 'name' => 'AAC Block 4"', 'unit' => 'Nos', 'spec' => '4 Inch'],
                ['label' => '6 Inch', 'name' => 'AAC Block 6"', 'unit' => 'Nos', 'spec' => '6 Inch'],
                ['label' => '8 Inch', 'name' => 'AAC Block 8"', 'unit' => 'Nos', 'spec' => '8 Inch'],
                ['label' => 'Concrete Block', 'name' => 'Solid Concrete Block', 'unit' => 'Nos', 'spec' => 'Concrete Block'],
                ['label' => 'Jointing Mortar', 'name' => 'Block Jointing Mortar', 'unit' => 'Bags', 'spec' => 'Mortar'],
            ]
        ],
        'Tiles' => [
            'description' => 'Vitrified Tiles, Wall Tiles, Granite, Marble',
            'items' => [
                ['label' => '2x2 Ft', 'name' => 'Vitrified Tiles 2x2', 'unit' => 'Box', 'spec' => '2x2 Ft'],
                ['label' => '2x4 Ft', 'name' => 'Vitrified Tiles 2x4', 'unit' => 'Box', 'spec' => '2x4 Ft'],
                ['label' => '12x18 Inch', 'name' => 'Wall Tiles 12x18', 'unit' => 'Box', 'spec' => '12x18 Inch'],
                ['label' => 'Granite', 'name' => 'Granite Slabs', 'unit' => 'Sq.Ft', 'spec' => 'Granite'],
                ['label' => 'Kota Stone', 'name' => 'Kota Stone', 'unit' => 'Sq.Ft', 'spec' => 'Kota Stone'],
                ['label' => 'Marble', 'name' => 'Marble Slabs', 'unit' => 'Sq.Ft', 'spec' => 'Marble'],
                ['label' => 'Tile Adhesive', 'name' => 'Tile Adhesive Chemical', 'unit' => 'Bags', 'spec' => 'Adhesive'],
                ['label' => 'Tile Grout', 'name' => 'Epoxy Tile Grout', 'unit' => 'Kg', 'spec' => 'Grout'],
            ]
        ],
        'Plumbing' => [
            'description' => 'CPVC/PVC Pipes, Water Tanks & Sanitary',
            'items' => [
                ['label' => '1/2 Inch (CPVC)', 'name' => 'CPVC Pipe 1/2"', 'unit' => 'Metres', 'spec' => '1/2 Inch'],
                ['label' => '3/4 Inch (CPVC)', 'name' => 'CPVC Pipe 3/4"', 'unit' => 'Metres', 'spec' => '3/4 Inch'],
                ['label' => '1 Inch (CPVC)', 'name' => 'CPVC Pipe 1"', 'unit' => 'Metres', 'spec' => '1 Inch'],
                ['label' => '1.5 Inch (CPVC)', 'name' => 'CPVC Pipe 1.5"', 'unit' => 'Metres', 'spec' => '1.5 Inch'],
                ['label' => '75mm (Drainage)', 'name' => 'PVC Drainage Pipe 75mm', 'unit' => 'Metres', 'spec' => '75mm'],
                ['label' => '110mm (Drainage)', 'name' => 'PVC Drainage Pipe 110mm', 'unit' => 'Metres', 'spec' => '110mm'],
                ['label' => '160mm (Drainage)', 'name' => 'PVC Drainage Pipe 160mm', 'unit' => 'Metres', 'spec' => '160mm'],
                ['label' => '500 Ltr (Tank)', 'name' => 'Overhead Water Tank 500L', 'unit' => 'Nos', 'spec' => '500 Litres'],
                ['label' => '1000 Ltr (Tank)', 'name' => 'Overhead Water Tank 1000L', 'unit' => 'Nos', 'spec' => '1000 Litres'],
                ['label' => 'Sanitary WC', 'name' => 'Western Closet EWC', 'unit' => 'Sets', 'spec' => 'EWC'],
                ['label' => 'Wash Basin', 'name' => 'Sanitary Wash Basin', 'unit' => 'Sets', 'spec' => 'Wash Basin'],
                ['label' => 'Stop Cock', 'name' => 'CP Brass Stop Cock', 'unit' => 'Nos', 'spec' => 'Valve'],
            ]
        ],
        'Electrical' => [
            'description' => 'Wires, Conduits, Switches & DB Boxes',
            'items' => [
                ['label' => '1.0 sq.mm (Wire)', 'name' => 'Copper Wire 1.0 sq.mm', 'unit' => 'Coils', 'spec' => '1.0 sq.mm'],
                ['label' => '1.5 sq.mm (Wire)', 'name' => 'Copper Wire 1.5 sq.mm', 'unit' => 'Coils', 'spec' => '1.5 sq.mm'],
                ['label' => '2.5 sq.mm (Wire)', 'name' => 'Copper Wire 2.5 sq.mm', 'unit' => 'Coils', 'spec' => '2.5 sq.mm'],
                ['label' => '4.0 sq.mm (Wire)', 'name' => 'Copper Wire 4.0 sq.mm', 'unit' => 'Coils', 'spec' => '4.0 sq.mm'],
                ['label' => '6.0 sq.mm (Wire)', 'name' => 'Copper Wire 6.0 sq.mm', 'unit' => 'Coils', 'spec' => '6.0 sq.mm'],
                ['label' => '20mm (Conduit)', 'name' => 'PVC Conduit Pipe 20mm', 'unit' => 'Nos', 'spec' => '20mm'],
                ['label' => '25mm (Conduit)', 'name' => 'PVC Conduit Pipe 25mm', 'unit' => 'Nos', 'spec' => '25mm'],
                ['label' => 'Switch Box', 'name' => 'Modular GI Switch Box', 'unit' => 'Nos', 'spec' => 'Switch Box'],
                ['label' => 'Switches / Sockets', 'name' => 'Modular Switches & Sockets', 'unit' => 'Nos', 'spec' => 'Modular'],
                ['label' => 'DB Box', 'name' => 'Distribution Board DB Box', 'unit' => 'Nos', 'spec' => 'DB Box'],
                ['label' => 'MCB', 'name' => 'MCB Single Pole', 'unit' => 'Nos', 'spec' => 'MCB'],
            ]
        ],
        'Paint' => [
            'description' => 'Putty, Primers, Emulsions & Waterproofing',
            'items' => [
                ['label' => 'Putty (40 Kg)', 'name' => 'Acrylic Wall Putty 40 Kg', 'unit' => 'Bags', 'spec' => '40 Kg'],
                ['label' => 'Primer', 'name' => 'Wall Primer', 'unit' => 'Litres', 'spec' => 'Primer'],
                ['label' => 'Exterior Paint', 'name' => 'Exterior Weatherproof Paint', 'unit' => 'Litres', 'spec' => 'Exterior'],
                ['label' => 'Interior Paint', 'name' => 'Interior Acrylic Emulsion Paint', 'unit' => 'Litres', 'spec' => 'Interior'],
                ['label' => 'Enamel Paint', 'name' => 'Synthetic Enamel Paint', 'unit' => 'Litres', 'spec' => 'Enamel'],
                ['label' => 'Dr. Fixit Chemical', 'name' => 'Waterproofing Chemical', 'unit' => 'Litres', 'spec' => 'Waterproofing'],
                ['label' => 'Shuttering Oil', 'name' => 'Shuttering Mould Oil', 'unit' => 'Litres', 'spec' => 'Oil'],
            ]
        ],
        'Hardware' => [
            'description' => 'Plywood, Steel Props, Scaffolding, Nails',
            'items' => [
                ['label' => '12mm (Plywood)', 'name' => 'Shuttering Plywood 12mm', 'unit' => 'Sheets', 'spec' => '12mm'],
                ['label' => '18mm (Plywood)', 'name' => 'Shuttering Plywood 18mm', 'unit' => 'Sheets', 'spec' => '18mm'],
                ['label' => 'Steel Props', 'name' => 'Adjustable Steel Props', 'unit' => 'Nos', 'spec' => 'Steel Props'],
                ['label' => 'Scaffolding Pipes', 'name' => 'MS Scaffolding Pipes', 'unit' => 'Nos', 'spec' => 'Scaffolding'],
                ['label' => 'Wire Nails', 'name' => 'Wire Nails', 'unit' => 'Kg', 'spec' => 'Wire Nails'],
                ['label' => 'Tie Rods', 'name' => 'Tie Rods & Wing Nuts', 'unit' => 'Nos', 'spec' => 'Tie Rods'],
            ]
        ],
        'RMC' => [
            'description' => 'Ready Mix Concrete grades for slab & structure',
            'items' => [
                ['label' => 'M-20', 'name' => 'Ready Mix Concrete M-20', 'unit' => 'CBM', 'spec' => 'M-20'],
                ['label' => 'M-25', 'name' => 'Ready Mix Concrete M-25', 'unit' => 'CBM', 'spec' => 'M-25'],
                ['label' => 'M-30', 'name' => 'Ready Mix Concrete M-30', 'unit' => 'CBM', 'spec' => 'M-30'],
                ['label' => 'M-35', 'name' => 'Ready Mix Concrete M-35', 'unit' => 'CBM', 'spec' => 'M-35'],
            ]
        ],
        'Safety' => [
            'description' => 'Helmets, Jackets, Shoes, Netting, Diesel',
            'items' => [
                ['label' => 'Helmets', 'name' => 'Safety Helmets', 'unit' => 'Nos', 'spec' => 'Helmet'],
                ['label' => 'Jackets', 'name' => 'Reflective Safety Jackets', 'unit' => 'Nos', 'spec' => 'Jacket'],
                ['label' => 'Safety Shoes', 'name' => 'Safety Shoes', 'unit' => 'Pairs', 'spec' => 'Shoes'],
                ['label' => 'Safety Net', 'name' => 'Green Safety Netting', 'unit' => 'Rolls', 'spec' => 'Net'],
                ['label' => 'Diesel', 'name' => 'Diesel Fuel', 'unit' => 'Litres', 'spec' => 'Diesel'],
            ]
        ]
    ];
}
