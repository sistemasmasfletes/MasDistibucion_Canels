function qrcode(codigo){
 
 
        $.post(urlGenerateQr,{
                        'id':codigo
                    },
                    function(data){
                    });
        
        function getDataUri(url, callback) {
            var image = new Image();

            image.onload = function () {
                var canvas = document.createElement('canvas');
                canvas.width = this.naturalWidth;
                canvas.height = this.naturalHeight;

                canvas.getContext('2d').drawImage(this, 0, 0);
                callback(canvas.toDataURL('image/png'));
            };
            setTimeout(function(){
                image.src = url;
            },1000);
            
        }

        
        getDataUri(qrcodePath+codigo+".png", function(dataUri) {
            var getLayout = function () {
                return {
                    hLineWidth: function () {
                        return 0.1;
                    },
                    vLineWidth: function () {
                        return 0.1;
                    },
                    hLineColor: function () {
                        return 'gray';
                    },
                    vLineColor: function () {
                        return 'gray';
                    }
                };
            };
            
            var getImage = function() {
                return [
                    { text: 'Orden de compra: ' + codigo , fontSize: 20, bold: false, alignment: 'center', style: ['lineSpacing', 'headingColor'] },
                    { canvas: [{ type: 'line', x1: 0, y1: 5, x2: 595 - 2 * 40, y2: 5, lineWidth: 1, lineColor: '#8B1616', style: ['lineSpacing'] }] },
                    { text:'',style:['doublelineSpacing'] },
                    { image: dataUri, layout: getLayout(), alignment: 'center' },
                    { text: codigo, alignment: 'center'}
                ];
            };
            
            var docDefinition = {
                    pageMargins: [72, 80, 40, 60],
                    layout: 'headerLineOnly',
                    pageSize: 'A4',
                    header: function() {
                        return {
                            columns: [
                                {
                                    text:'Mas Distribución' ,
                                    width: 200,
                                    margin: [50, 20, 5, 5]
                                }
                            ]
                        }
                    },

                    footer: function (currentPage, pageCount) {
                        return {
                            stack: [{ canvas: [{ type: 'line', x1: 0, y1: 5, x2: 595, y2: 5, lineWidth: 1, lineColor: '#8B1616', style: ['lineSpacing'] }] },
                            { text: '', margin: [0, 0, 0, 5] },
                            {
                                columns: [
                                    {},
                                    { text: currentPage.toString(), alignment: 'center' },
                                    { text: moment(new Date()).format("DD-MMM-YYYY"), alignment: 'right', margin: [0, 0, 20, 0] }
                                ]
                            }]

                        };
                    },
                content: [
                    {stack:getImage()}
                ],
                styles: {
                    'lineSpacing': {
                        margin: [0, 0, 0, 6]
                    },
                    'doublelineSpacing': {
                        margin: [0, 0, 0, 12]
                    },
                    'headingColor':
                    {
                        color: '#999966'
                    },
                    tableHeader: {
                        bold: true,
                        fontSize: 13,
                        color: '#669999'
                    }
                }
            }
            
            pdfMake.createPdf(docDefinition).download('QRCode_'+codigo+'.pdf');
        });
    };