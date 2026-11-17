
// Máscara de CNPJ
const campoCnpj = document.getElementById('cnpj');

if (campoCnpj) {
    campoCnpj.addEventListener('input', function () {
        let valor = this.value.replace(/\D/g, '').slice(0, 14);

        valor = valor.replace(/^(\d{2})(\d)/, '$1.$2');
        valor = valor.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        valor = valor.replace(/\.(\d{3})(\d)/, '.$1/$2');
        valor = valor.replace(/(\d{4})(\d)/, '$1-$2');

        this.value = valor;
    });
}

// Máscara de telefone
const campoTelefone = document.getElementById('telefone');

if (campoTelefone) {
    campoTelefone.addEventListener('input', function () {
        let valor = this.value.replace(/\D/g, '').slice(0, 11);

        if (valor.length > 10) {
            valor = valor.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
        } else if (valor.length > 6) {
            valor = valor.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3');
        } else if (valor.length > 2) {
            valor = valor.replace(/^(\d{2})(\d+)/, '($1) $2');
        } else if (valor.length > 0) {
            valor = valor.replace(/^(\d+)/, '($1');
        }

        this.value = valor;
    });
}
