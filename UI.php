<?php

namespace PetakUmpet;

/* How does our UI works?

Application->render
           ->setTemplate
           ->Template->render
             Template->setUI 
                     ->setLayout
                     ->render
                       Response->render


*/
                       
abstract class UI {
  
}
