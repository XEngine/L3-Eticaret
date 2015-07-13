    <?php  
    class diskUsage  
    {  
     function __construct( $disk = '.' )  
     {  
      $this->the_drive = $disk;  
      $this->raw_diskspace = $this->disk_spaces( "total" );  
      $this->raw_freespace = $this->disk_spaces( "free" );  
      $this->readable_diskspace = $this->readableSizes( $this->raw_diskspace );  
      $this->readable_freespace = $this->readableSizes( $this->raw_freespace );  
      $this->percentage_free = $this->percentages( "free" );  
      $this->percentage_used = $this->percentages( "used" );  
     }  
       
     public function disk_spaces( $type )  
     {  
      switch($type)  
      {  
       case "total":  
        return disk_total_space( $this->the_drive );  
       break;  
       case "free":  
        return disk_free_space( $this->the_drive );  
       break;  
      }  
     }  
       
     public function readableSizes( $size )  
     {  
      $types = array( ' B', ' KB', ' MB', ' GB', ' TB', ' TB', ' EB', ' ZB', ' YB' );  
      $i=0;  
      while($size>=1024)  
      {  
       $size/=1024;  
       $i++;  
      }  
      return("".round($size,2).$types[$i]);  
     }  
       
     public function percentages( $type )  
     {  
      switch($type)  
      {  
       case "free":  
        return (round($this->raw_freespace / $this->raw_diskspace, 2) * 100) . "%";  
       break;  
       case "used":  
        return round(100 - $this->percentage_free) . "%";  
       break;  
      }  
     }  
    }