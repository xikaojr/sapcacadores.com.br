/**
 * moeda
 *
 * @abstract Classe que formata de desformata valores monet�rios em float e formata valores
 * de float em moeda.
 *
 * @author anselmo
 *
 * @email anselmobattisti arroba gmail.com
 *
 * @site battisti.etc.br
 *
 * @example
 * 		moeda.formatar(1000)
 *      	>> retornar 1.000,00
 *
 * 		moeda.desformatar(1.000,00)
 * 		>> retornar 1000
 *
 * @version 1.0
 **/
var moeda = {

    /**
	 * retiraFormatacao
	 *
	 * Remove a formata��o de uma string de moeda e retorna um float
	 *
	 * @param {Object} num
	 */
    desformatar: function(num){

        if(!num) {
            num = "0";
        }

        num = num.replace(/ /g,"");
        num = num.replace(Itarget.lang.price,"");
        num = num.replace(/\./g,"");
        num = num.replace(",",".");
        num = num.replace(/[^0-9\.]/g,"");

        return parseFloat(num);
    },

    /**
	 * formatar
	 *
	 * Deixar um valor float no formato monet�rio
	 *
	 * @param {Object} num
	 */
    formatar: function(num){
        x = 0;

        if(num<0){
            num = Math.abs(num);
            x = 1;
        }

        if(isNaN(num)) num = "0";
        cents = Math.floor((num*100+0.5)%100);

        num = Math.floor((num*100+0.5)/100).toString();

        if(cents < 10) cents = "0" + cents;
        for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
            num = num.substring(0,num.length-(4*i+3))+'.'
            +num.substring(num.length-(4*i+3));

        ret = num + ',' + cents;

        if (x == 1) ret = ' - ' + ret;
        return ret;
    },

    /**
	 * arredondar
	 *
	 * @abstract Arredonda um valor quebrado para duas casas decimais.
	 *
	 * @param {Object} num
	 */
    arredondar: function(num){
        return Math.round(num*Math.pow(10,2))/Math.pow(10,2);
    }
}
