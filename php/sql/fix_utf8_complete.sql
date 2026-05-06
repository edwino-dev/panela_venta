USE ventas_panela;

-- Eliminar y recrear productos con codificación UTF-8 correcta
DELETE FROM products;

INSERT INTO products (name, short_description, description, price, stock, image) VALUES
('Panela en Bloque',
 '100% natural y artesanal. Obtenida del jugo de caña de azúcar evaporado y solidificado de manera tradicional.',
 'Panela artesanal elaborada mediante el proceso tradicional de evaporación del jugo de caña de azúcar. Conserva todos sus nutrientes y su sabor auténtico.\n\nBeneficios:\n• Sin químicos ni aditivos\n• Fuente de energía natural\n• Rica en minerales como hierro, calcio y magnesio\n\nUso recomendado:\nIdeal para bebidas calientes, postres y recetas tradicionales.',
 12000, 50, 'panela_bloque_1kg.png'),

('Panela Granulada',
 'Natural, práctica y deliciosa. Panela pulverizada que se disuelve fácilmente, con el mismo sabor y beneficios de la panela tradicional.',
 'Panela granulada artesanal que mantiene todas las propiedades nutricionales de la panela tradicional pero con la ventaja de su presentación pulverizada.\n\nBeneficios:\n• Fácil disolución y uso\n• Sin refinar, sin blanquear\n• Aporta hierro y minerales esenciales\n\nUso recomendado:\nPerfecta para café, té, batidos, repostería y cocina saludable.',
 7000, 80, 'panela_granulada_500g.png'),

('Panela Líquida',
 'Sabor natural en cada gota. Jarabe líquido de panela, obtenido del jugo de caña evaporado.',
 'Panela líquida elaborada a partir del jugo de caña evaporado, manteniendo todos los beneficios y sabor auténtico de la panela tradicional en formato líquido.\n\nBeneficios:\n• Lista para usar\n• Sin químicos ni conservantes\n• Endulza de manera saludable\n\nUso recomendado:\nEndulza tus preparaciones de forma saludable y natural. Ideal para bebidas, postres y cocina.',
 9500, 30, 'panela_liquida_250ml.png');
