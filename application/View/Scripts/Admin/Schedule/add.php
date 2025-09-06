<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner">
                <h1>Programación de rutas</h1>
                <?php
                echo '<a class="btn" href="' . $view->url(array('action' => 'route', 'id' => $view->route->getId())) . '" >Regresar</a>';
                echo '<div class="clear" style="height:15px;" ></div>';
                if($view->result === true)
                {
                    echo '<div class="alert alert-success">';
                    echo 'Registro insertado con exito';
                    echo '</div>';
                }
                else if($view->result === false)
                {
                    echo '<div class="alert alert-info">';
                    echo 'Formato de fecha incorrecto';
                    echo '</div>';
                }
                ?>
                <form method="post" >
                    <label>Fecha (yyyy-mm-dd)</label>
                    <input type="text" name="date" id="dateRoot"/>
                    <label>Hora</label>
                    <select class="span1" name="hours" >
                        <option>00</option>
                        <option>01</option>
                        <option>02</option>
                        <option>03</option>
                        <option>04</option>
                        <option>05</option>
                        <option>06</option>
                        <option>07</option>
                        <option>08</option>
                        <option>09</option>
                        <option>10</option>
                        <option>11</option>
                        <option>12</option>
                        <option>13</option>
                        <option>14</option>
                        <option>15</option>
                        <option>16</option>
                        <option>17</option>
                        <option>18</option>
                        <option>19</option>
                        <option>20</option>
                        <option>21</option>
                        <option>22</option>
                        <option>23</option>
                    </select>
                    :
                    <select class="span1" name="mins" >
                        <option>00</option>
                        <option>05</option>
                        <option>10</option>
                        <option>15</option>
                        <option>20</option>
                        <option>25</option>
                        <option>30</option>
                        <option>35</option>
                        <option>40</option>
                        <option>45</option>
                        <option>50</option>
                        <option>55</option>
                    </select>
                    <br/>
                    <button type="submit" class="btn">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
