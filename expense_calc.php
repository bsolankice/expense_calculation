<?php 
/**
 * Author: Bhavin Solanki
 * Date: 05th Sep 2018
 * Purpose: To calculate expense and settlement 
 */

$calc = new ExpenseCalculation();
$calc->calculateExpense();

/**
 * This class include all the required function related
 * to expense calculation.
 */
class ExpenseCalculation{

    protected $persons = ['A' , 'B' ,'C','D'];
    protected $owes = [];
    protected $expenses = [
            1 => [
                'payer' => 'A',
                'amount' => '40000',
                'peoples' => ['A' , 'B' , 'C' ,'D']
            ],
            2 => [
                'payer' => 'B',
                'amount' => '4000',
                'peoples' => ['A' , 'B']
            ],
            3 => [
                'payer' => 'B',
                'amount' => '2000',
                'peoples' => ['B' , 'C']
            ],
            4 => [
                'payer' => 'C',
                'amount' => '13000',
                'peoples' => ['B','A']
            ],
            5 => [
                'payer' => 'A',
                'amount' => '13000',
                'peoples' => ['D']
            ],
            6 => [
                'payer' => 'D',
                'amount' => '13000',
                'peoples' => ['A']
            ]
        ];

    function __construct() {

        echo "\n\t** Start calculation *********************\n";

        // Calculate individual total amount
        $this->individualTotalPaidAmount();

        // Calculate overall total amount
        $this->totalAmount();

    }

    function __destruct(){
        echo "\n\t** End calculation ***********************\n\n";
    }

    /**
     * Calculate Total amount
     */    
    protected function totalAmount(){
               
        $totalAmount = array_sum(
            array_map(function($expenses) { 
                return $expenses['amount']; 
            }, $this->expenses)
        );

        echo $this->seprator();
        echo "\n\tTotal Amount is \t\t$totalAmount";
        echo $this->seprator();

    }       

    protected function seprator(){
        return "\n\t------------------------------------------";
    }

    /**
     * Calculate Total Amount paid by per person
     */
    protected function individualTotalPaidAmount(){

        foreach ($this->persons as $person) {
           
            $totalAmount = 0;
            $totalAmount = array_sum(
                    array_map(function($expenses) use ($person) { 
                        return $expenses['payer'] == $person 
                                    ? $expenses['amount'] 
                                    : 0; 
                    }, $this->expenses)
            );

            echo "\n\tTotal Amount paid by $person is \t$totalAmount";
        }
        echo "\n";

    }

    /**
     * Find per person amount from perticular expense amount
     */
    protected function findPerPersonAmount($expense){
        return number_format(
                (float)$expense['amount'] / count ($expense['peoples']), 2, '.', ''
            );
    }

    /**
     * Function to calculate expense
     */
    public function calculateExpense(){
        
        foreach ($this->expenses as $expense) {

            // Find per person amount from perticular expense amount
            $perPersonAmount = $this->findPerPersonAmount($expense); 

            foreach ($expense['peoples'] as $person) {
                
                // Exclude entry if payer and included person is same
                if($expense['payer'] == $person)
                    continue;

                if(!isset($this->owes[$expense['payer']]))
                    $this->owes[$expense['payer']] = [];
                
                if(!isset($this->owes[$expense['payer']][$person]))
                    $this->owes[$expense['payer']][$person] = 0;
                
                // Find if any transaction happens between two person
                if(isset($this->owes[$person][$expense['payer']])){

                    // Find if any person already own any amount
                    if($this->owes[$person][$expense['payer']] < $perPersonAmount){
                        $diffAmount = $perPersonAmount - $this->owes[$person][$expense['payer']];
                        unset($this->owes[$person][$expense['payer']]);

                        // Add difference amount
                        $this->owes[$expense['payer']][$person] = $diffAmount;
                    }else{

                        // Deduct amount from own amount
                        $this->owes[$person][$expense['payer']] -= $perPersonAmount;
                    }

                }else{

                    // Add amount
                    $this->owes[$expense['payer']][$person] += $perPersonAmount;
                }                                
            }
        }

        // Display Owes amount
        $this->displayOwesAmount();

    }

    /**
     * Display Owes Amount
     */
    protected function displayOwesAmount(){

        echo "\n\n";
        foreach ($this->owes as $owePerson => $persons) {
            
            foreach ($persons as $person => $amount) {
                
                // Skip if found entry with 0 amount
                if($amount != 0)
                    echo "\t$owePerson owes $person \t\t\t$amount\n";
                
            }            
        }
    }
}
?>