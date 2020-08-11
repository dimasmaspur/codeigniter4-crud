<?php namespace App\Controllers;

class Pages extends BaseController
{
	public function index()
	{
        $data = [
            'title' => 'Home | Dimas Purwanto'
        ];
		return view('pages/home',$data);
	}
    public function about()
	{
        $data = [
            'title' => 'About | Dimas Purwanto'
        ];
     
		return view('pages/about',$data);
    }
   
    public function contact(){
        $data = [
            'title' => 'About | Dimas Purwanto',
            'alamat' => [
                [ 
                    'tipe'=>'rumah',
                    'alamat'=>'jl bandung no2',
                    'kota'=>'bandung' 
                ],
                [
                        'tipe'=>'kantor',
                    'alamat'=>'jl jakarta no5',
                    'kota'=>'jakarta'
                ]
            ]
        ];

        return view('pages/contact',$data);
    }
	
	//--------------------------------------------------------------------

}
