// assets/js/masks.js

// Função genérica para aplicar máscara a um campo
const applyMask = (input, mask) => {
    input.addEventListener('input', () => {
        // Remove todos os caracteres que não são dígitos
        const cleanedValue = input.value.replace(/\D/g, '');
        let maskedValue = '';
        let digitIndex = 0;

        // Itera sobre o padrão da máscara
        for (let i = 0; i < mask.length && digitIndex < cleanedValue.length; i++) {
            if (mask[i] === '#') {
                maskedValue += cleanedValue[digitIndex];
                digitIndex++;
            } else {
                maskedValue += mask[i];
            }
        }
        input.value = maskedValue;
    });
};

// Aguarda o carregamento do DOM para adicionar os listeners
document.addEventListener('DOMContentLoaded', () => {
    const cpfInput = document.getElementById('cpf');
    const telefoneInput = document.getElementById('telefone');

    // Aplica as máscaras se os campos existirem na página atual
    if (cpfInput) {
        applyMask(cpfInput, '###.###.###-##');
    }

    if (telefoneInput) {
        // Esta máscara cobre o formato (XX) XXXXX-XXXX
        applyMask(telefoneInput, '(##) #####-####');
    }
});