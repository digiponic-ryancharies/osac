<table class="table" style="width:100%; text-align: center">    
    <tbody>
        <tr>
            <th colspan="2">
                <h2>BARCODE DAFTAR PRODUK</h2>
            </th>
        </tr>
        @for ($i = 0; $i < $count; $i=$i+2)            
            <tr style="padding-bottom: 30px">
                <td>
                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($barcode[$i], 'C128',2,70) }}" alt="{{ $barcode[$i] }}"/>
                    <p>{{ $barcode[$i] }} <br> {{ $text[$i] }}</p>                    
                    <br>
                </td>
                <td>
                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($barcode[$i+1], 'C128',2,70) }}" alt="{{ $barcode[$i+1] }}"/>
                    <p>{{ $barcode[$i+1] }} <br> {{ $text[$i+1] }}</p>
                    <br>
                </td>                
            </tr>        
        @endfor
    </tbody>
</table>

<script src="{{ asset('js/additional/jquery-1.11.3.min.js') }}"></script>
<script>
    $(function(){
        window.print();
    });
</script>