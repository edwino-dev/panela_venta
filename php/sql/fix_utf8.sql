USE ventas_panela;

-- Actualizar descripciones con codificación UTF-8 correcta
UPDATE products 
SET name = 'Panela en Bloque',
    short_description = '100% natural y artesanal. Obtenida del jugo de caña de azúcar evaporado y solidificado de manera tradicional.',
    description = 'Panela artesanal elaborada mediante el proceso tradicional de evaporación del jugo de caña de azúcar. Conserva todos sus nutrientes y su sabor auténtico.

Beneficios:
• Sin químicos ni aditivos
• Fuente de energía natural
• Rica en minerales como hierro, calcio y magnesio

Uso recomendado:
Ideal para bebidas calientes, postres y recetas tradicionales.'
WHERE id = 1;

UPDATE products 
SET name = 'Panela Granulada',
    short_description = 'Natural, práctica y deliciosa. Panela pulverizada que se disuelve fácilmente, con el mismo sabor y beneficios de la panela tradicional.',
    description = 'Panela granulada artesanal que mantiene todas las propiedades nutricionales de la panela tradicional pero con la ventaja de su presentación pulverizada.

Beneficios:
• Fácil disolución y uso
• Sin refinar, sin blanquear
• Aporta hierro y minerales esenciales

Uso recomendado:
Perfecta para café, té, batidos, repostería y cocina saludable.'
WHERE id = 2;

UPDATE products 
SET name = 'Panela Líquida',
    short_description = 'Sabor natural en cada gota. Jarabe líquido de panela, obtenido del jugo de caña evaporado.',
    description = 'Panela líquida elaborada a partir del jugo de caña evaporado, manteniendo todos los beneficios y sabor auténtico de la panela tradicional en formato líquido.

Beneficios:
• Lista para usar
• Sin químicos ni conservantes
• Endulza de manera saludable

Uso recomendado:
Endulza tus preparaciones de forma saludable y natural. Ideal para bebidas, postres y cocina.'
WHERE id = 3;
