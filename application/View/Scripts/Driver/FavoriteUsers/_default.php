<div class="container">
    <div class="row" >
        <div class="span12 blockGray">
            <div class="blockInner span4">
                <h1>Favoritos</h1>
                <?php 
                    if($view->user instanceof DefaultDb_Entities_User)
                    {
                        echo 'Cliente: '.$view->user->getFullName();
                    }
                ?>
                <?php if($view->favoriteUsersClient) : ?>
                    <br/>
                    <table class="table table-striped table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>Clientes Favorito</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            foreach($view->favoriteUsersClient as $user)
                            {
                                echo '<tr>';
                                echo '<td>';
                                $urlFavoriteUsers = $view->url(array('module'=>'User', 'controller'=>'Store', 'action'=>'user', 'id'=>$user->getFavoriteClient()->getId()));
                                echo '<a href="'.$urlFavoriteUsers.'">'.$user->getFavoriteClient()->getCommercialName().'</a>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <div class="alert alert-block">
                        No se encontraron Clientes favoritos.
                    </div>
                <?php endif; ?>
                     <?php if($view->favoriteUsersSuppliers) : ?>
                    <br/>
                    <table class="table table-striped table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>Proveedores Favorito</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            foreach($view->favoriteUsersSuppliers as $user)
                            {
                                echo '<tr>';
                                echo '<td>';
                                $urlFavoriteUsers = $view->url(array('module'=>'User', 'controller'=>'Store', 'action'=>'user', 'id'=>$user->getFavoriteClient()->getId()));
                                echo '<a href="'.$urlFavoriteUsers.'">'.$user->getFavoriteClient()->getCommercialName().'</a>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <div class="alert alert-block">
                        No se encontraron Proveedores favoritos.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>