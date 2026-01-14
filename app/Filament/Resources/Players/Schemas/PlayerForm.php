<?php

namespace App\Filament\Resources\Players\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PlayerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->description('Youth basic data')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Full name'),
                        
                        // REPLACE: TextInput for age with DatePicker for birthdate
                        DatePicker::make('birthdate')
                            ->required()
                            ->label('Date of Birth')
                            ->displayFormat('d/m/Y')
                            ->maxDate(now()->subYears(12)) // Minimum 12 years old
                            ->helperText('Age will be calculated automatically')
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $age = Carbon::parse($state)->age;
                                    $set('age_display', $age . ' years old');
                                }
                            }),
                        
                        // Display calculated age
                        Placeholder::make('age_display')
                            ->label('Age')
                            ->content(fn ($get) => 
                                $get('birthdate') 
                                    ? Carbon::parse($get('birthdate'))->age . ' years old'
                                    : 'Select birthdate'
                            ),
                        
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+1234567890'),
                        
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('example@email.com'),
                    ])->columns(2),
                
                Section::make('Competition Participation')
                    ->schema([
                        Select::make('team_id')
                            ->label('Team')
                            ->relationship('team', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                ColorPicker::make('color')
                                    ->default('#3b82f6'),
                            ])
                            ->createOptionUsing(function (array $data) {
                                return \App\Models\Team::create($data)->id;
                            })
                            ->helperText('Select a team or create a new one'),
                        
                        Toggle::make('active')
                            ->default(true)
                            ->label('Actively participates')
                            ->helperText('If inactive, won\'t appear in weeks'),
                    ]),
            ]);
    }
}
