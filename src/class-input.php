<?php
namespace RUpdater;

class Input {
    /**
     * Renders a HTML text field
     *
     * @param $id string HTML id of the element
     * @param $name string HTML name of the element
     * @param $value string Default value of the element (optional)
     */
    public static function text( $id, $name, $value='' ) {
        printf( '<input type="text" id="%s" name="%s" value="%s" />', $id, $name, $value );
    }

    /**
     * Renders a HTML text field
     *
     * @param $id string HTML id of the element
     * @param $name string HTML name of the element
     * @param $options array Key-value pair with the selector options
     * @param $selected string Pre-selected value (optional)
     * @example
     *  array (
     *   'option_value' => 'Option text'
     *  );
     */
    public static function select( $id, $name, $options, $selected='' ) {
        ?>
        <select id="<?php echo $id ?>" name="<?php echo $name ?>">
            <?php foreach( $options as $key => $value ) {
                printf( '<option value="%s" %s>%s</option>', $key, selected( $key, $selected ), ucfirst( $value ) );
            } ?>
        </select>
        <?php
    }
}