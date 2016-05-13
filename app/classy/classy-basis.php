<?php 

/**
 * Includes Basic Class methods that are common used in other classes
 */

class ClassyBasis {

	protected function import($data) {

        if ( is_object( $data ) ) {
        
            $data = get_object_vars( $data );
        
        }

        if ( is_array( $data ) ) {
            
            foreach ( $data as $key => $value ) {
            
                if ( !empty( $key ) ) {
            
                    $this->$key = $value;
            
                } else if ( !empty( $key ) && !method_exists($this, $key) ){
            
                    $this->$key = $value;
            
                }
            }

        }

	}

}